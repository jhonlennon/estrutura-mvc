<?php

    class ImagensModel extends Model {

        protected $Table = 'imagens';
        protected $ValueObject = 'ImageVO';
        protected $ImgExtensions = ['gif', 'jpg', 'png', 'jpeg'];

        /**
         * Retorna lista de imagens referente 
         * @param string $Ref Referência/Tabela
         * @param int $RefId ID de referência
         * @param boolean $Default Retorna uma imagem default
         * @param boolean $onlyActive Retornar só ativos?
         * @return array|ImageVO
         */
        function getByRef($Ref, $RefId = 0, $Default = false, $onlyActive = true) {
            $Lista = $this->Lista('WHERE (a.ref = :ref AND a.refid = :refid)  AND ((:status = "all" AND a.status = 1) OR a.status = :status) ORDER BY a.position ASC', [
                'ref' => $Ref,
                'refid' => $RefId,
                'status' => $onlyActive ? 1 : 'all',
                    ], false, true);
            if (!$Lista and $Default) {
                $Lista[] = $this->newValueObject();
            }
            return $Lista;
        }

        /**
         * Excluí todas as imagens da referencia
         * @param string $ref
         * @param int $refId
         */
        function excluirTodasImagens($ref, $refId) {
            foreach ($this->Lista('WHERE a.ref = :ref AND a.refid = :id', ['ref' => $ref, 'id' => $refId]) as $img) {
                $this->excluirImage($img);
            }
        }

        /**
         * Excluí a imagem da galeria
         * @param int $id
         * @return string|boolean
         */
        function excluirImage(ImageVO $img) {
            $deleta = $this->Excluir($img->getId());
            $this->unlinkImage($img->getSource());
        }

        /**
         * Salva a imagem
         * @param ImageVO $img
         * @param type $fileInput
         * @param type $last
         * @return boolean
         */
        function salvaImage(ImageVO $img, $fileInput = null, $last = true) {
            $files = $this->getArrayFiles($fileInput);
            if ($img->getId()) {
                if (count($files)) {
                    if ($source = $this->moveFile($files[0])) {
                        $img->setSource($source);
                    }
                }
                return $this->Save($img);
            } else if ($files) {
                $count = 0;
                foreach ($files as $file) {
                    if ($source = $this->moveFile($file)) {
                        $newImg = $img->cloneVo();
                        $newImg->setSource($source);
                        $newImg->setPosition($last ? 9999 : 0);
                        $this->Save($newImg);
                        $count++;
                    }
                }
                $this->reorganizar($img->getRef(), $img->getRefId());
                return $count ? true : false;
            }
            return false;
        }

        /**
         * Envia novas imagens
         * @param type $ref
         * @param type $refId
         * @param type $fileInput
         * @param type $last
         * @return boolean
         */
        function uploadImage($ref, $refId, $fileInput, $last = true) {
            if (!$files = $this->getArrayFiles($fileInput)) {
                return false;
            }

            foreach ($files as $file) {
                if ($source = $this->moveFile($file)) {
                    $this->Save($this->novoRegistro([
                                'position' => $last ? 9999 : 0,
                                'ref' => $ref,
                                'refid' => $refId,
                                'source' => $file['name'],
                    ]));
                }
            }

            #Reoganizando imagens
            $this->reorganizar($ref, $refId);

            return true;
        }

        /**
         * Deleta o arquivo na pasta
         * @param string $name
         * @return boolean
         */
        private function unlinkImage($name) {
            $path = self::getImagePath() . DIRECTORY_SEPARATOR . $name;
            if (!preg_match('/default/', $path) and file_exists($path) and is_file($path)) {
                return unlink($path);
            }
            return false;
        }

        /**
         * Retorna uma array com todos os arquivos enviados
         * @param string $fileInputKeyName
         * @return array
         */
        public function getArrayFiles($fileInputKeyName) {
            $files = isset($_FILES[$fileInputKeyName]) ? $_FILES[$fileInputKeyName] : null;
            if ($files) {
                if (is_array($files['tmp_name'])) {
                    $array = array();
                    foreach ($files['tmp_name'] as $key => $tmp) {
                        //verifica: Error, isImage, extension
                        $extension = Utils::getFileExtension($files['name'][$key]);
                        if ($files['error'][$key] == UPLOAD_ERR_OK and ( $size = getimagesize($tmp)) and in_array($extension, $this->ImgExtensions)) {
                            $array[] = [
                                'path' => $tmp,
                                'name' => date('Ymd_') . uniqid() . '.' . $extension,
                                'size' => $files['size'][$key],
                                'width' => $size[0],
                                'height' => $size[1],
                            ];
                        }
                    }
                    return empty($array) ? null : $array;
                }
                //verifica: Error, isImage, extension                
                else if ($files['error'] == UPLOAD_ERR_OK and ( $size = getimagesize($files['tmp_name']))) {
                    $extension = Utils::getFileExtension($files['name']);
                    if (in_array($extension, $this->ImgExtensions)) {
                        return [
                            [
                                'path' => $files['tmp_name'],
                                'name' => date('Ymd_') . uniqid() . '.' . $extension,
                                'size' => $files['size'],
                                'width' => $size[0],
                                'height' => $size[1],
                            ]
                        ];
                    }
                }
            }
            return null;
        }

        public static function getImagePath() {
            $path = abs_source_images();
            if (!file_exists($path)) {
                mkdir($path, 0777);
            }
            return $path;
        }

        private function moveFile($file) {
            if ($file['size'] > 500 * KB) {

                if ($file['size'] > 2 * MB) {
                    $quality = 60;
                } else if ($file['size'] > 1 * MB) {
                    $quality = 80;
                } else {
                    $quality = 100;
                }


                if ($file['width'] > 2000 or $file['height'] > 200) {
                    if ($file['width'] > $file['height']) {
                        $scale = 2000 / $file['width'];
                    } else {
                        $scale = 2000 / $file['height'];
                    }
                    $file['width'] = $file['width'] * $scale;
                    $file['height'] = $file['height'] * $scale;
                }
                
                return (new IMGCanvas($file['path']))
                                ->hexa('#FFFFFF')
                                ->redimensiona($file['width'], $file['height'], 'proporcional')
                                ->grava(self::getImagePath() . DIRECTORY_SEPARATOR . $file['name'], $quality) ? $file['name'] : null;
                
            } else {
                return move_uploaded_file($file['path'], self::getImagePath() . DIRECTORY_SEPARATOR . $file['name']) ? $file['name'] : null;
            }
        }

        /**
         * Reorganiza as imagens na tabela
         * @param string $ref
         * @param int $refId
         */
        function reorganizar($ref, $refId) {
            foreach ($this->Lista('WHERE a.ref = :ref AND a.refid = :refid AND a.status != 99 ORDER BY a.position ASC', ['ref' => $ref, 'refid' => $refId]) as $i => $img) {
                $img->setPosition($i + 1);
                $this->Save($img);
            }
        }

        /**
         * Retorna a imagem de capa do registro
         * @param string $ref
         * @param int $refId
         * @param boolean $default
         * @return ImageVO|null
         */
        function capa($ref, $refId, $default = true) {
            $busca = $this->Lista('WHERE a.ref = :ref AND a.refid = :id AND a.status = 1 ORDER BY a.position ASC LIMIT 1', ['ref' => $ref, 'id' => $refId]);
            if (count($busca)) {
                return $busca[0];
            } else if ($default) {
                return $this->newValueObject();
            }
            return null;
        }

    }
    