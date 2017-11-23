<?php

class m_dingcan_company_attr extends m_common
{
    function __construct()
    {
        parent::__construct();
    }


    function get_by_guid($company_guid){
        return $this->query_one("select * from dingcan_company_attr where company_guid='".$company_guid."' ");
    }

}