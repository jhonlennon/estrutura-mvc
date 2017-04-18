<?php

    class databaseController extends Controller {

        function indexAction() {
            header('Location: ' . url());
        }

        function backupAction() {

            $Read = APP::getInstance('Read');

            header('Content-type: text/sql; charset=UTF-8;');

            ob_start();

            foreach (Conn::getTables() as $table) {
                # Drop Table
                echo "DROP TABLE IF EXISTS `{$table}`;\n";

                # Create Table
                $queryCrete = $Read->FullRead("SHOW CREATE TABLE `{$table}`", ['table' => $table])->getResult()[0]['Create Table'];
                $to = ['/CREATE TABLE/', '/AUTO_INCREMENT=[0-9]+/', '/CHARSET\=[a-z0-9]+?;/'];
                $from = ['CREATE TABLE IF NOT EXISTS', null, 'CHARSET=utf8;'];
                echo preg_replace($to, $from, "{$queryCrete};") . "\n";

                # Registros
                $Registros = $Read->FullRead("SELECT * FROM `{$table}`;")->getResult();
                $Total = count($Registros);
                foreach ($Registros as $i => $r) {
                    if (!$i or $i % 100 == 0) {
                        if ($i) {
                            echo ";\n";
                        }
                        # Display Insert Query
                        echo "INSERT INTO `{$table}` (`" . implode('`, `', array_keys($r)) . "`) VALUES ";
                    } else if ($i) {
                        echo ', ';
                    }
                    # Formatando
                    foreach ($r as $key => $value) {
                        $r[$key] = Conn::getConn()->quote($value, 2);
                    }
                    # Display Insert Values
                    echo "(" . implode(", ", array_values($r)) . ")";
                }
                if ($i == ($Total - 1)) {
                    echo ';';
                }
                echo "\n";
                if ($Total) {
                    echo "\n";
                }
            }

            # SqlQuery
            $sqlQuery = ob_get_clean();

            # Save Path Temporary
            $path = ABSPATH . '/temp/';

            # File SQL
            $file = Conn::getDataBaseName() . '.sql';
            file_put_contents("{$path}{$file}", $sqlQuery);

            # File ZIPing
            $zip = new ZipArchive;
            try {
                $zip->open("{$path}{$file}.zip", ZipArchive::CREATE);
                $zip->addFile("{$path}{$file}", basename($file));
            } catch (Exception $ex) {
                exit(var_dump($ex));
            }
            $zip->close();

            # Efetuando download
            header("Content-Disposition: attachment; filename=\"{$file}.zip\"");
            readfile("{$path}{$file}.zip");

            # Excluíndo arquivos
            unlink("{$path}{$file}");
            unlink("{$path}{$file}.zip");
        }

    }
    