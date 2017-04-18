<?php

    class SiteConfigModel extends Model {

	protected $Table = 'site_config';
	protected $ValueObject = 'SiteConfigVO';

	/** @return SiteConfigVO */
	function get() {
	    return $this->Lista('ORDER BY a.id DESC LIMIT 1')[0];
	}

    }
    