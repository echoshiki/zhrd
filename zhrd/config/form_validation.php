<?php

$config = array(
                 'form' => array(
                                    array(
                                            'field' => 'areaid[]',
                                            'label' => '所属地区',
                                            'rules' => 'required|select_check[0]|xss_clean|trim'
                                         ),
                                   /*  array(
                                            'field' => 'form[COMPANY]',
                                            'label' => '公司名称',
                                            'rules' => 'alpha_sub|required|min_length[8]|max_length[100]|xss_clean|trim'
                                         ),
                                    array(
                                            'field' => 'form[LICENCE]',
                                            'label' => '营业执照',
                                            'rules' => 'trim|required|alpha_numeric|exact_length[15]|numeric|xss_clean|trim'
                                         ), */
                                    array(
                                            'field' => 'trade[]',
                                            'label' => '所属行业',
                                            'rules' => 'required|select_check[0]'
                                         ),
									array(
                                            'field' => 'form[SALE_LAST]',
                                            'label' => '上年度销售收入',
                                            'rules' => 'numeric|max_length[10]|xss_clean|trim'
                                         ),
									array(
                                            'field' => 'form[DEMAND]',
                                            'label' => '授信需求',
                                            'rules' => 'numeric|max_length[10]|xss_clean|trim'
                                         ),		

                                    ),

                //小型企业科技类型验证规则
                 '44'   => array(
                                    array(
                                            'field' => 'form[MAIN]',
                                            'label' => '主营业务',
                                            'rules' => 'required|max_length[300]|xss_clean|trim'
                                         ),
                                    array(
                                            'field' => 'form[PLACE]',
                                            'label' => '行业地位',
                                            'rules' => 'required'
                                         ),
                                    array(
                                            'field' => 'form[ISVARIOUS]',
                                            'label' => '是否为高新',
                                            'rules' => 'required'
                                         ),
                                    array(
                                            'field' => 'form[TALLENT][]',
                                            'label' => '所属于人才计划',
                                            'rules' => 'required'
                                         ),
                                    array(
                                            'field' => 'form[TALLENT][about]',
                                            'label' => '所属其他人才计划',
                                            'rules' => 'max_length[60]|xss_clean|trim'
                                         ),
                                    array(
                                            'field' => 'form_main[SALE_THIS]',
                                            'label' => '本年度预计销售收入',
                                            'rules' => 'required|max_length[10]|numeric|xss_clean|trim'
                                         ),
                                    array(
                                            'field' => 'form_main[COST]',
                                            'label' => '销售成本',
                                            'rules' => 'required|max_length[10]|numeric|xss_clean|trim'
                                         ),
                                    array(
                                            'field' => 'form_main[PROFIT]',
                                            'label' => '净利润',
                                            'rules' => 'required|max_length[10]|numeric|xss_clean|trim'
                                         ),
                                    array(
                                            'field' => 'form_main[ASSET]',
                                            'label' => '企业资产总额',
                                            'rules' => 'required|max_length[10]|numeric|xss_clean|trim'
                                         ),
                                    array(
                                            'field' => 'form_main[DEBT]',
                                            'label' => '企业负债总额',
                                            'rules' => 'required|max_length[10]|numeric|xss_clean|trim'
                                         ),
                                    array(
                                            'field' => 'form_main[ACCOUNT_LAST_C]',
                                            'label' => '去年末应收账款余额',
                                            'rules' => 'required|max_length[10]|numeric|xss_clean|trim'
                                         ),
                                    array(
                                            'field' => 'form_main[ACCOUNT_BEFORE_C]',
                                            'label' => '前年末应收账款余额',
                                            'rules' => 'required|max_length[10]|numeric|xss_clean|trim'
                                         ),
                                    array(
                                            'field' => 'form_main[STOCK_LAST]',
                                            'label' => '去年末存货余额',
                                            'rules' => 'required|max_length[10]|numeric|xss_clean|trim'
                                         ),
                                    array(
                                            'field' => 'form_main[STOCK_BEFORE]|numeric|xss_clean|trim',
                                            'label' => '前年末存货余额',
                                            'rules' => 'required|max_length[10]|numeric|xss_clean|trim'
                                         ),
                                    array(
                                            'field' => 'form_main[ACCOUNT_LAST_P]',
                                            'label' => '去年年末应付账款余额',
                                            'rules' => 'required|max_length[10]|numeric|xss_clean|trim'
                                         ),
                                    array(
                                            'field' => 'form_main[ACCOUNT_BEFORE_P]',
                                            'label' => '前年年末应付账款余额',
                                            'rules' => 'required|max_length[10]|numeric|xss_clean|trim'
                                         ),
                                    array(
                                            'field' => 'form[USE]',
                                            'label' => '资金用途',
                                            'rules' => 'required|max_length[300]|xss_clean|trim'
                                         ),
                                    array(
                                            'field' => 'form[COOPERATION][]',
                                            'label' => '合作银行',
                                            'rules' => 'required'
                                         ),
                                    array(
                                            'field' => 'form_main[FINANCE_THIS]',
                                            'label' => '目前在外融资金额',
                                            'rules' => 'required|max_length[10]|numeric|xss_clean|trim'
                                         ),
                                    array(
                                            'field' => 'form[OWNER_TIME]',
                                            'label' => '企业主从业年限',
                                            'rules' => 'required|max_length[5]|numeric|xss_clean|trim'
                                         ),
                                    array(
                                            'field' => 'form[COM_TIME]',
                                            'label' => '企业经营年限',
                                            'rules' => 'required|max_length[5]|numeric|xss_clean|trim'
                                         ),
                                    array(
                                            'field' => 'form[STAFF]',
                                            'label' => '企业员工数',
                                            'rules' => 'required|max_length[10]|numeric|xss_clean|trim'
                                         ),
                                    array(
                                            'field' => 'form[ISLIST]',
                                            'label' => '是否有上市预期',
                                            'rules' => 'required'
                                         ),
                                    array(
                                            'field' => 'form[BELONG]',
                                            'label' => '经营场所权属',
                                            'rules' => 'required'
                                         ),
                                    array(
                                            'field' => 'form_main[ASSET_FIXED]',
                                            'label' => '固定资产',
                                            'rules' => 'required|max_length[10]|numeric|xss_clean|trim'
                                         ),
                                    array(
                                            'field' => 'warrant[]',
                                            'label' => '担保方式',
                                            'rules' => 'required|xss_clean|trim'
                                         ),
                                    array(
                                            'field' => 'form[ISRELATE]',
                                            'label' => '是否愿意连带责任',
                                            'rules' => 'required|select_check[0]|xss_clean|trim'
                                         ),
                                    array(
                                            'field' => 'form_main[TEL]',
                                            'label' => '联系方式',
                                            'rules' => 'required|numeric|max_length[20]|xss_clean|trim'
                                         ),

                                    ),

                
                //小型企业文化类型验证规则
                 '45'   => array(
                                    array(
                                            'field' => 'form[MAIN]',
                                            'label' => '主营业务',
                                            'rules' => 'required|max_length[300]|xss_clean|trim'
                                         ),
                                    array(
                                            'field' => 'form[PLACE]',
                                            'label' => '行业地位',
                                            'rules' => 'required'
                                         ),
                                    array(
                                            'field' => 'form[TRADE_2][]',
                                            'label' => '文化行业类别',
                                            'rules' => 'required'
                                         ),
                                    array(
                                            'field' => 'form_main[SALE_THIS]',
                                            'label' => '本年度预计销售收入',
                                            'rules' => 'required|max_length[10]|numeric|xss_clean|trim'
                                         ),
                                    array(
                                            'field' => 'form_main[COST]',
                                            'label' => '销售成本',
                                            'rules' => 'required|max_length[10]|numeric|xss_clean|trim'
                                         ),
                                    array(
                                            'field' => 'form_main[PROFIT]',
                                            'label' => '净利润',
                                            'rules' => 'required|max_length[10]|numeric|xss_clean|trim'
                                         ),
                                    array(
                                            'field' => 'form_main[ASSET]',
                                            'label' => '企业资产总额',
                                            'rules' => 'required|max_length[10]|numeric|xss_clean|trim'
                                         ),
                                    array(
                                            'field' => 'form_main[DEBT]',
                                            'label' => '企业负债总额',
                                            'rules' => 'required|max_length[10]|numeric|xss_clean|trim'
                                         ),
                                    array(
                                            'field' => 'form_main[ACCOUNT_LAST_C]',
                                            'label' => '去年末应收账款余额',
                                            'rules' => 'required|max_length[10]|numeric|xss_clean|trim'
                                         ),
                                    array(
                                            'field' => 'form_main[ACCOUNT_BEFORE_C]',
                                            'label' => '前年末应收账款余额',
                                            'rules' => 'required|max_length[10]|numeric|xss_clean|trim'
                                         ),
                                    array(
                                            'field' => 'form_main[STOCK_LAST]',
                                            'label' => '去年末存货余额',
                                            'rules' => 'required|max_length[10]|numeric|xss_clean|trim'
                                         ),
                                    array(
                                            'field' => 'form_main[STOCK_BEFORE]',
                                            'label' => '前年末存货余额',
                                            'rules' => 'required|max_length[10]|numeric|xss_clean|trim'
                                         ),
                                    array(
                                            'field' => 'form_main[ACCOUNT_LAST_P]',
                                            'label' => '去年年末应付账款余额',
                                            'rules' => 'required|max_length[10]|numeric|xss_clean|trim'
                                         ),
                                    array(
                                            'field' => 'form_main[ACCOUNT_BEFORE_P]',
                                            'label' => '前年年末应付账款余额',
                                            'rules' => 'required|max_length[10]|numeric|xss_clean|trim'
                                         ),
                                    array(
                                            'field' => 'form[USE]',
                                            'label' => '资金用途',
                                            'rules' => 'required|max_length[300]|xss_clean|trim'
                                         ),
                                    array(
                                            'field' => 'form[COOPERATION][]',
                                            'label' => '合作银行',
                                            'rules' => 'required'
                                         ),
                                    array(
                                            'field' => 'form_main[FINANCE_THIS]',
                                            'label' => '目前在外融资金额',
                                            'rules' => 'required|max_length[10]|numeric|xss_clean|trim'
                                         ),
                                    array(
                                            'field' => 'form[OWNER_HOME]',
                                            'label' => '企业主籍贯',
                                            'rules' => 'required|max_length[20]|xss_clean|trim'
                                         ),
                                    array(
                                            'field' => 'form[COM_HONOR][]',
                                            'label' => '荣誉称号',
                                            'rules' => 'required'
                                         ),
                                    array(
                                            'field' => 'form[OWNER_TIME]',
                                            'label' => '企业主从业年限',
                                            'rules' => 'required|max_length[5]|numeric|xss_clean|trim'
                                         ),
                                    array(
                                            'field' => 'form[COM_TIME]',
                                            'label' => '企业经营年限',
                                            'rules' => 'required|max_length[5]|numeric|xss_clean|trim'
                                         ),
                                    array(
                                            'field' => 'form[STAFF]',
                                            'label' => '企业员工数',
                                            'rules' => 'required|max_length[10]|numeric|xss_clean|trim'
                                         ),
                                    array(
                                            'field' => 'form[ISLIST]',
                                            'label' => '是否有上市预期',
                                            'rules' => 'required'
                                         ),
                                    array(
                                            'field' => 'form[BELONG]',
                                            'label' => '经营场所权属',
                                            'rules' => 'required'
                                         ),
                                    array(
                                            'field' => 'form_main[ASSET_FIXED]',
                                            'label' => '固定资产',
                                            'rules' => 'required|max_length[10]|numeric|xss_clean|trim'
                                         ),
                                    array(
                                            'field' => 'warrant[]',
                                            'label' => '担保方式',
                                            'rules' => 'required'
                                         ),
                                    array(
                                            'field' => 'form[ISRELATE]',
                                            'label' => '是否愿意连带责任',
                                            'rules' => 'required|select_check[0]|xss_clean|trim'
                                         ),
                                    array(
                                            'field' => 'form_main[TEL]',
                                            'label' => '联系方式',
                                            'rules' => 'required|max_length[20]|numeric|xss_clean|trim'
                                         ),

                                    ),


                //小型企业商贸类型验证规则
                 '46'   => array(
                                    array(
                                            'field' => 'form[MAIN]',
                                            'label' => '主营业务',
                                            'rules' => 'required|max_length[300]|xss_clean|trim'
                                         ),
                                    array(
                                            'field' => 'form[PLACE]',
                                            'label' => '行业地位',
                                            'rules' => 'required'
                                         ),
                                    array(
                                            'field' => 'form[BRAND]',
                                            'label' => '品牌代理商',
                                            'rules' => 'required'
                                         ),
                                    array(
                                            'field' => 'form_main[SALE_THIS]',
                                            'label' => '本年度预计销售收入',
                                            'rules' => 'required|max_length[10]|numeric|xss_clean|trim'
                                         ),
                                    array(
                                            'field' => 'form_main[COST]',
                                            'label' => '销售成本',
                                            'rules' => 'required|max_length[10]|numeric|xss_clean|trim'
                                         ),
                                    array(
                                            'field' => 'form_main[PROFIT]',
                                            'label' => '净利润',
                                            'rules' => 'required|max_length[10]|numeric|xss_clean|trim'
                                         ),
                                    array(
                                            'field' => 'form_main[ASSET]',
                                            'label' => '企业资产总额',
                                            'rules' => 'required|max_length[10]|numeric|xss_clean|trim'
                                         ),
                                    array(
                                            'field' => 'form_main[DEBT]',
                                            'label' => '企业负债总额',
                                            'rules' => 'required|max_length[10]|numeric|xss_clean|trim'
                                         ),
                                    array(
                                            'field' => 'form_main[ACCOUNT_LAST_C]',
                                            'label' => '去年末应收账款余额',
                                            'rules' => 'required|max_length[10]|numeric|xss_clean|trim'
                                         ),
                                    array(
                                            'field' => 'form_main[ACCOUNT_BEFORE_C]',
                                            'label' => '前年末应收账款余额',
                                            'rules' => 'required|max_length[10]|numeric|xss_clean|trim'
                                         ),
                                    array(
                                            'field' => 'form_main[STOCK_LAST]',
                                            'label' => '去年末存货余额',
                                            'rules' => 'required|max_length[10]|numeric|xss_clean|trim'
                                         ),
                                    array(
                                            'field' => 'form_main[STOCK_BEFORE]',
                                            'label' => '前年末存货余额',
                                            'rules' => 'required|max_length[10]|numeric|xss_clean|trim'
                                         ),
                                    array(
                                            'field' => 'form_main[ACCOUNT_LAST_P]',
                                            'label' => '去年年末应付账款余额',
                                            'rules' => 'required|max_length[10]|numeric|xss_clean|trim'
                                         ),
                                    array(
                                            'field' => 'form_main[ACCOUNT_BEFORE_P]',
                                            'label' => '前年年末应付账款余额',
                                            'rules' => 'required|max_length[10]|numeric|xss_clean|trim'
                                         ),
                                    array(
                                            'field' => 'form[USE]',
                                            'label' => '资金用途',
                                            'rules' => 'required|max_length[300]|xss_clean|trim'
                                         ),
                                    array(
                                            'field' => 'form[COOPERATION][]',
                                            'label' => '合作银行',
                                            'rules' => 'required'
                                         ),
                                    array(
                                            'field' => 'form_main[FINANCE_THIS]',
                                            'label' => '目前在外融资金额',
                                            'rules' => 'required|max_length[10]|numeric|xss_clean|trim'
                                         ),
                                    array(
                                            'field' => 'form[OWNER_HOME]',
                                            'label' => '企业主籍贯',
                                            'rules' => 'required|max_length[20]|xss_clean|trim'
                                         ),
                                    array(
                                            'field' => 'form[OWNER_TIME]',
                                            'label' => '企业主从业年限',
                                            'rules' => 'required|max_length[5]|numeric|xss_clean|trim'
                                         ),
                                    array(
                                            'field' => 'form[COM_TIME]',
                                            'label' => '企业经营年限',
                                            'rules' => 'required|max_length[5]|numeric|xss_clean|trim'
                                         ),
                                    array(
                                            'field' => 'form[STAFF]',
                                            'label' => '企业员工数',
                                            'rules' => 'required|max_length[10]|numeric|xss_clean|trim'
                                         ),
                                    array(
                                            'field' => 'form[ISLIST]',
                                            'label' => '是否有上市预期',
                                            'rules' => 'required'
                                         ),
                                    array(
                                            'field' => 'form[BELONG]',
                                            'label' => '经营场所权属',
                                            'rules' => 'required'
                                         ),
                                    // array(
                                    //         'field' => 'form_main[ASSET_FIXED]',
                                    //         'label' => '固定资产',
                                    //         'rules' => 'required|max_length[10]|numeric|xss_clean|trim'
                                    //      ),
                                    array(
                                            'field' => 'warrant[]',
                                            'label' => '担保方式',
                                            'rules' => 'required'
                                         ),
                                    array(
                                            'field' => 'form[ISRELATE]',
                                            'label' => '是否愿意连带责任',
                                            'rules' => 'required|select_check[0]|xss_clean|trim'
                                         ),
                                    array(
                                            'field' => 'form_main[TEL]',
                                            'label' => '联系方式',
                                            'rules' => 'required|max_length[20]|numeric|xss_clean|trim'
                                         ),

                                    ),


                //小型企业三农类型验证规则
                 '47'   => array(
                                    array(
                                            'field' => 'form[MAIN]',
                                            'label' => '主营业务',
                                            'rules' => 'required|max_length[300]|xss_clean|trim'
                                         ),
                                    array(
                                            'field' => 'form[PLACE]',
                                            'label' => '行业地位',
                                            'rules' => 'required'
                                         ),
                                    array(
                                            'field' => 'form_main[SALE_THIS]',
                                            'label' => '本年度预计销售收入',
                                            'rules' => 'required|max_length[10]|numeric|xss_clean|trim'
                                         ),
                                    array(
                                            'field' => 'form[V_TOTAL]',
                                            'label' => '村名下资产总量',
                                            'rules' => 'required|max_length[10]|numeric|xss_clean|trim'
                                         ),
                                    array(
                                            'field' => 'form[V_SALE_LAST]',
                                            'label' => '去年末村可支配收入',
                                            'rules' => 'required|max_length[10]|numeric|xss_clean|trim'
                                         ),
                                    array(
                                            'field' => 'form[V_SALE_BEFORE]',
                                            'label' => '前年末村可支配收入',
                                            'rules' => 'required|max_length[10]|numeric|xss_clean|trim'
                                         ),
                                    array(
                                            'field' => 'form[USE]',
                                            'label' => '资金用途',
                                            'rules' => 'required|max_length[300]|xss_clean|trim'
                                         ),
                                    array(
                                            'field' => 'form[COOPERATION][]',
                                            'label' => '合作银行',
                                            'rules' => 'required'
                                         ),
                                    array(
                                            'field' => 'form_main[FINANCE_THIS]',
                                            'label' => '目前在外融资金额',
                                            'rules' => 'required|max_length[10]|numeric|xss_clean|trim'
                                         ),
                                    array(
                                            'field' => 'warrant[]',
                                            'label' => '担保方式',
                                            'rules' => 'required'
                                         ),
                                    array(
                                            'field' => 'form[ISRELATE]',
                                            'label' => '是否愿意连带责任',
                                            'rules' => 'required|select_check[0]|xss_clean|trim'
                                         ),
                                    array(
                                            'field' => 'form_main[TEL]',
                                            'label' => '联系方式',
                                            'rules' => 'required|max_length[20]|numeric|xss_clean|trim'
                                         ),

                                    ),

                //小型企业其他类型验证规则
                 '48'   => array(
                                    array(
                                            'field' => 'form[MAIN]',
                                            'label' => '主营业务',
                                            'rules' => 'required|max_length[300]|xss_clean|trim'
                                         ),
                                    array(
                                            'field' => 'form[PLACE]',
                                            'label' => '行业地位',
                                            'rules' => 'required'
                                         ),
                                    array(
                                            'field' => 'form_main[SALE_THIS]',
                                            'label' => '本年度预计销售收入',
                                            'rules' => 'required|numeric|max_length[10]|xss_clean|trim'
                                         ),
                                    array(
                                            'field' => 'form_main[COST]',
                                            'label' => '销售成本',
                                            'rules' => 'required|numeric|max_length[10]|xss_clean|trim'
                                         ),
                                    array(
                                            'field' => 'form_main[PROFIT]',
                                            'label' => '净利润',
                                            'rules' => 'required|numeric|max_length[10]|xss_clean|trim'
                                         ),
                                    array(
                                            'field' => 'form_main[ASSET]',
                                            'label' => '企业资产总额',
                                            'rules' => 'required|numeric|max_length[10]|xss_clean|trim'
                                         ),
                                    array(
                                            'field' => 'form_main[DEBT]',
                                            'label' => '企业负债总额',
                                            'rules' => 'required|numeric|max_length[10]|xss_clean|trim'
                                         ),
                                    array(
                                            'field' => 'form_main[ACCOUNT_LAST_C]',
                                            'label' => '去年末应收账款余额',
                                            'rules' => 'required|numeric|max_length[10]|xss_clean|trim'
                                         ),
                                    array(
                                            'field' => 'form_main[ACCOUNT_BEFORE_C]',
                                            'label' => '前年末应收账款余额',
                                            'rules' => 'required|numeric|max_length[10]|xss_clean|trim'
                                         ),
                                    array(
                                            'field' => 'form_main[STOCK_LAST]',
                                            'label' => '去年末存货余额',
                                            'rules' => 'required|numeric|max_length[10]|xss_clean|trim'
                                         ),
                                    array(
                                            'field' => 'form_main[STOCK_BEFORE]',
                                            'label' => '前年末存货余额',
                                            'rules' => 'required|numeric|max_length[10]|xss_clean|trim'
                                         ),
                                    array(
                                            'field' => 'form_main[ACCOUNT_LAST_P]',
                                            'label' => '去年年末应付账款余额',
                                            'rules' => 'required|numeric|max_length[10]|xss_clean|trim'
                                         ),
                                    array(
                                            'field' => 'form_main[ACCOUNT_BEFORE_P]',
                                            'label' => '前年年末应付账款余额',
                                            'rules' => 'required|numeric|max_length[10]|xss_clean|trim'
                                         ),
                                    array(
                                            'field' => 'form[USE]',
                                            'label' => '资金用途',
                                            'rules' => 'required|max_length[300]|xss_clean|trim'
                                         ),
                                    array(
                                            'field' => 'form[COOPERATION][]',
                                            'label' => '合作银行',
                                            'rules' => 'required'
                                         ),
                                    array(
                                            'field' => 'form_main[FINANCE_THIS]',
                                            'label' => '目前在外融资金额',
                                            'rules' => 'required|numeric|max_length[10]|xss_clean|trim'
                                         ),
                                    array(
                                            'field' => 'form[OWNER_TIME]',
                                            'label' => '企业主从业年限',
                                            'rules' => 'required|numeric|max_length[5]|xss_clean|trim'
                                         ),
                                    array(
                                            'field' => 'form[COM_TIME]',
                                            'label' => '企业经营年限',
                                            'rules' => 'required|numeric|max_length[5]|xss_clean|trim'
                                         ),
                                    array(
                                            'field' => 'form[STAFF]',
                                            'label' => '企业员工数',
                                            'rules' => 'required|numeric|max_length[10]|xss_clean|trim'
                                         ),
                                    array(
                                            'field' => 'form[ISLIST]',
                                            'label' => '是否有上市预期',
                                            'rules' => 'required|select_check[0]|xss_clean|trim'
                                         ),
                                    array(
                                            'field' => 'form[BELONG]',
                                            'label' => '经营场所权属',
                                            'rules' => 'required'
                                         ),
                                    array(
                                            'field' => 'form_main[ASSET_FIXED]',
                                            'label' => '固定资产',
                                            'rules' => 'required|numeric|max_length[10]|xss_clean|trim'
                                         ),
                                    array(
                                            'field' => 'warrant[]',
                                            'label' => '担保方式',
                                            'rules' => 'required'
                                         ),
                                    array(
                                            'field' => 'form[ISRELATE]',
                                            'label' => '是否愿意连带责任',
                                            'rules' => 'required|select_check[0]|xss_clean|trim'
                                         ),
                                    array(
                                            'field' => 'form_main[TEL]',
                                            'label' => '联系方式',
                                            'rules' => 'required|max_length[20]|xss_clean|trim'
                                         ),

                                    ),
                
                //中型企业验证规则
                 'form_mid_1'  => array(
                                    array(
                                            'field' => 'form[ISAUDIT]',
                                            'label' => '是否审计',
                                            'rules' => 'required'
                                         ),
                                    array(
                                            'field' => 'form[ASSET][]',
                                            'label' => '资产总额',
                                            'rules' => 'required|numeric|max_length[10]|xss_clean|trim'
                                         ),
                                    array(
                                            'field' => 'form[DEBT][]',
                                            'label' => '负债总额',
                                            'rules' => 'required|numeric|max_length[10]|xss_clean|trim'
                                         ),
                                    array(
                                            'field' => 'form[OWNER_RIGHTS][]',
                                            'label' => '所有者权益',
                                            'rules' => 'required|numeric|max_length[10]|xss_clean|trim'
                                         ),
                                    array(
                                            'field' => 'form[ACCOUNT_C][0]',
                                            'label' => '当前月报应收账款',
                                            'rules' => 'required|numeric|max_length[10]|xss_clean|trim'
                                         ),
                                    array(
                                            'field' => 'form[ACCOUNT_C][1]',
                                            'label' => '上年月报应收账款',
                                            'rules' => 'required|numeric|max_length[10]|xss_clean|trim'
                                         ),
                                    array(
                                            'field' => 'form[ACCOUNT_C][2]',
                                            'label' => '前年月报应收账款',
                                            'rules' => 'required|numeric|max_length[10]|xss_clean|trim'
                                         ),
                                    array(
                                            'field' => 'form[ACCOUNT_P][]',
                                            'label' => '应付账款',
                                            'rules' => 'required|numeric|max_length[10]|xss_clean|trim'
                                         ),
                                    array(
                                            'field' => 'form[STOCK][]',
                                            'label' => '存货余额以及补充说明',
                                            'rules' => 'required|xss_clean|trim'
                                         ),
                                    array(
                                            'field' => 'form[MAIN_SALE][]',
                                            'label' => '主营业务销售收入',
                                            'rules' => 'required|numeric|max_length[10]|xss_clean|trim'
                                         ),
                                    array(
                                            'field' => 'form[COST][]',
                                            'label' => '销售成本',
                                            'rules' => 'required|numeric|max_length[10]|xss_clean|trim'
                                         ),
                                    array(
                                            'field' => 'form[PROFIT][]',
                                            'label' => '净利润',
                                            'rules' => 'required|numeric|max_length[10]|xss_clean|trim'
                                         ),
                                    array(
                                            'field' => 'form[CASHFLOW][1]',
                                            'label' => '经营性净现金流',
                                            'rules' => 'required|numeric|max_length[10]|xss_clean|trim'
                                         ),
                                    array(
                                            'field' => 'form[CASHFLOW][0]',
                                            'label' => '经营性净现金流',
                                            'rules' => 'numeric|max_length[10]|xss_clean|trim'
                                         ),
                                    array(
                                            'field' => 'form[ACCOUNT_OC][0]',
                                            'label' => '当前月报其他应收款',
                                            'rules' => 'required|numeric|max_length[10]|xss_clean|trim'
                                         ),
                                    array(
                                            'field' => 'form[ACCOUNT_OC][1]',
                                            'label' => '上年月报其他应收款',
                                            'rules' => 'required|numeric|max_length[10]|xss_clean|trim'
                                         ),
                                    array(
                                            'field' => 'form[INVESTMENT][0]',
                                            'label' => '当前月报短期和长期投资',
                                            'rules' => 'required|numeric|max_length[10]|xss_clean|trim'
                                         ),
                                    array(
                                            'field' => 'form[INVESTMENT][1]',
                                            'label' => '上年月报短期和长期投资',
                                            'rules' => 'required|numeric|max_length[10]|xss_clean|trim'
                                         ),
                                    array(
                                            'field' => 'form[SHORT][0]',
                                            'label' => '当前月报银行短期借款',
                                            'rules' => 'required|numeric|max_length[10]|xss_clean|trim'
                                         ),
                                    array(
                                            'field' => 'form[SHORT][1]',
                                            'label' => '上年月报银行短期借款',
                                            'rules' => 'required|numeric|max_length[10]|xss_clean|trim'
                                         ),
                                    array(
                                            'field' => 'form[LONG][0]',
                                            'label' => '当前月报银行长期借款',
                                            'rules' => 'required|numeric|max_length[10]|xss_clean|trim'
                                         ),
                                    array(
                                            'field' => 'form[LONG][1]',
                                            'label' => '上年月报银行长期借款',
                                            'rules' => 'required|numeric|max_length[10]|xss_clean|trim'
                                         ),

                                    array(
                                            'field' => 'form[PAYABLE][0]',
                                            'label' => '当前月报应付票据',
                                            'rules' => 'required|numeric|max_length[10]|xss_clean|trim'
                                         ),
                                    array(
                                            'field' => 'form[PAYABLE][1]',
                                            'label' => '上年月报应付票据',
                                            'rules' => 'required|numeric|max_length[10]|xss_clean|trim'
                                         ),                                     

                                    array(
                                            'field' => 'form[ACCOUNT_OP][0]',
                                            'label' => '当前月报其他应付款',
                                            'rules' => 'required|numeric|max_length[10]|xss_clean|trim'
                                         ),
                                    array(
                                            'field' => 'form[ACCOUNT_OP][1]',
                                            'label' => '上年月报其他应付款',
                                            'rules' => 'required|numeric|max_length[10]|xss_clean|trim'
                                         ),  

                                    array(
                                            'field' => 'form[ASSET_FIXED][]',
                                            'label' => '固定资产',
                                            'rules' => 'required|numeric|max_length[10]|xss_clean|trim'
                                         ),
                                    array(
                                            'field' => 'form[FINANCE_THIS]',
                                            'label' => '目前在外融资金额',
                                            'rules' => 'required|numeric|max_length[10]|xss_clean|trim'
                                         ),
                                    array(
                                            'field' => 'form[MATERIAL][]',
                                            'label' => '存货余额中原材料',
                                            'rules' => 'required|numeric|max_length[10]|xss_clean|trim'
                                         ),
                                    array(
                                            'field' => 'form[FIELD_RIGHTS][]',
                                            'label' => '无形土地使用权',
                                            'rules' => 'required|max_length[10]|xss_clean|trim'
                                         ),
                                    array(
                                            'field' => 'form[ACCOUNT_OP_S][]',
                                            'label' => '其他应付款中股东借款',
                                            'rules' => 'required|numeric|max_length[10]|xss_clean|trim'
                                         ),
                                    array(
                                            'field' => 'form[SALE_THIS]',
                                            'label' => '预计今年销售收入',
                                            'rules' => 'required|numeric|max_length[10]|xss_clean|trim'
                                         ),
                                   ),


                 'form_mid_2'  => array(
                                    array(
                                            'field' => 'form[OWNER_HOME]',
                                            'label' => '企业主籍贯',
                                            'rules' => 'required|max_length[60]|xss_clean|trim'
                                         ),
                                    array(
                                            'field' => 'form[OWNER_TIME]',
                                            'label' => '企业主从业年限',
                                            'rules' => 'required|xss_clean|trim'
                                         ),
                                    array(
                                            'field' => 'form[EXPERIENCE][0][]',
                                            'label' => '企业主从业经历',
                                            'rules' => 'required|max_length[200]|xss_clean|trim'
                                         ),
                                    array(
                                            'field' => 'form[CERTIFICATE][0][]',
                                            'label' => '资质证书',
                                            'rules' => 'required|max_length[200]|xss_clean|trim'
                                         ),
                                    array(
                                            'field' => 'form[COM_TIME]',
                                            'label' => '企业经营年限',
                                            'rules' => 'required|xss_clean|trim'
                                         ),
                                    array(
                                            'field' => 'form[STAFF]',
                                            'label' => '员工数',
                                            'rules' => 'required|xss_clean|trim'
                                         ),
                                    array(
                                            'field' => 'form[BELONG]',
                                            'label' => '经营场所权属',
                                            'rules' => 'required'
                                         ),
                                    array(
                                            'field' => 'form[MAIN]',
                                            'label' => '主营业务',
                                            'rules' => 'required|max_length[300]|xss_clean|trim'
                                         ),
                                    array(
                                            'field' => 'form[PLACE]',
                                            'label' => '行业地位',
                                            'rules' => 'required|xss_clean|trim'
                                         ),
                                    array(
                                            'field' => 'form[ISLIST]',
                                            'label' => '上市情况',
                                            'rules' => 'required|xss_clean|trim'
                                         ),
                                    array(
                                            'field' => 'form[USE]',
                                            'label' => '资金用途',
                                            'rules' => 'required|max_length[300]|xss_clean|trim'
                                         ),
                                    ),

                 'form_mid_3'  => array(
                                    array(
                                            'field' => 'warrant[]',
                                            'label' => '担保方式及相关选项',
                                            'rules' => 'required|xss_clean|trim'
                                         ),
                                    array(
                                            'field' => 'warrant[price]',
                                            'label' => '评估价值',
                                            'rules' => 'max_length[10]|numeric|xss_clean|trim'
                                         ),
                                    array(
                                            'field' => 'warrant[price2]',
                                            'label' => '第三方销售收入',
                                            'rules' => 'max_length[10]|numeric|xss_clean|trim'
                                         ),
                                    array(
                                            'field' => 'warrant[good]',
                                            'label' => '质押物',
                                            'rules' => 'max_length[100]|xss_clean|trim'
                                         ),
                                    array(
                                            'field' => 'warrant[about]',
                                            'label' => '对外担保情况',
                                            'rules' => 'max_length[100]|xss_clean|trim'
                                         ),

                                    array(
                                            'field' => 'form[DEMAND_O][]',
                                            'label' => '授信情况以及相关选项',
                                            'rules' => 'required|max_length[200]|xss_clean|trim'
                                         ),

                                    array(
                                            'field' => 'form[DEMAND_O][about2]',
                                            'label' => '授信总量',
                                            'rules' => 'max_length[10]|numeric|xss_clean|trim'
                                         ),

                                    array(
                                            'field' => 'form[ISRELATE]',
                                            'label' => '是否愿意连带责任担保',
                                            'rules' => 'required|select_check[0]|xss_clean|trim'
                                         ),
                                    array(
                                            'field' => 'form[TEL]',
                                            'label' => '联系方式',
                                            'rules' => 'required'
                                         ),
                                    array(
                                            'field' => 'form[THUMB2]',
                                            'label' => '其他补充情况',
                                            'rules' => 'max_length[300]|xss_clean|trim'
                                         ),
 
                                    ),



                 'edit_com' => array(
                                    array(
                                            'field' => 'form_main[AREAID]',
                                            'label' => '所属地区',
                                            'rules' => 'required'
                                         ),
                                    array(
                                            'field' => 'form_main[COMPANY]',
                                            'label' => '公司名称',
                                            'rules' => 'required|min_length[8]|max_length[100]|alpha_sub|xss_clean|trim'
                                         ),
                                    array(
                                            'field' => 'form_main[LICENCE]',
                                            'label' => '营业执照',
                                            'rules' => 'required|alpha_numeric|exact_length[15]|numeric|xss_clean|trim'
                                         ),
                                    array(
                                            'field' => 'form_main[TEL]',
                                            'label' => '联系方式',
                                            'rules' => 'required|max_length[20]|xss_clean|trim'
                                         ),

                                    array(
                                            'field' => 'form_main[SALE_LAST]',
                                            'label' => '上年度销售收入',
                                            'rules' => 'numeric|max_length[10]|xss_clean|trim'
                                         ),
									array(
                                            'field' => 'form_main[DEMAND]',
                                            'label' => '授信需求',
                                            'rules' => 'numeric|max_length[10]|xss_clean|trim'
                                         ),	


                                    //comSmall
                                    array(
                                            'field' => 'form[MAIN]',
                                            'label' => '主营业务',
                                            'rules' => 'max_length[300]|xss_clean|trim'
                                         ),
                                    array(
                                            'field' => 'form[PLACE]',
                                            'label' => '行业地位',
                                            'rules' => 'xss_clean|trim'
                                         ),


                                    array(
                                            'field' => 'form_main[SALE_THIS]',
                                            'label' => '本年度预计销售收入',
                                            'rules' => 'max_length[10]|numeric|xss_clean|trim'
                                         ),
                                    array(
                                            'field' => 'form_main[COST]',
                                            'label' => '销售成本',
                                            'rules' => 'max_length[10]|numeric|xss_clean|trim'
                                         ),
                                    array(
                                            'field' => 'form_main[PROFIT]',
                                            'label' => '净利润',
                                            'rules' => 'max_length[10]|numeric|xss_clean|trim'
                                         ),
                                    array(
                                            'field' => 'form_main[ASSET]',
                                            'label' => '企业资产总额',
                                            'rules' => 'max_length[10]|numeric|xss_clean|trim'
                                         ),
                                    array(
                                            'field' => 'form_main[DEBT]',
                                            'label' => '企业负债总额',
                                            'rules' => 'max_length[10]|numeric|xss_clean|trim'
                                         ),
                                    array(
                                            'field' => 'form_main[ACCOUNT_LAST_C]',
                                            'label' => '去年末应收账款余额',
                                            'rules' => 'max_length[10]|numeric|xss_clean|trim'
                                         ),
                                    array(
                                            'field' => 'form_main[ACCOUNT_BEFORE_C]',
                                            'label' => '前年末应收账款余额',
                                            'rules' => 'max_length[10]|numeric|xss_clean|trim'
                                         ),
                                    array(
                                            'field' => 'form_main[STOCK_LAST]',
                                            'label' => '去年末存货余额',
                                            'rules' => 'max_length[10]|numeric|xss_clean|trim'
                                         ),
                                    array(
                                            'field' => 'form_main[STOCK_BEFORE]',
                                            'label' => '前年末存货余额',
                                            'rules' => 'max_length[10]|numeric|xss_clean|trim'
                                         ),
                                    array(
                                            'field' => 'form_main[ACCOUNT_LAST_P]',
                                            'label' => '去年年末应付账款余额',
                                            'rules' => 'max_length[10]|numeric|xss_clean|trim'
                                         ),
                                    array(
                                            'field' => 'form_main[ACCOUNT_BEFORE_P]',
                                            'label' => '前年年末应付账款余额',
                                            'rules' => 'max_length[10]|numeric|xss_clean|trim'
                                         ),
                                    array(
                                            'field' => 'form[USE]',
                                            'label' => '资金用途',
                                            'rules' => 'max_length[300]|xss_clean|trim'
                                         ),
                                    array(
                                            'field' => 'form[COOPERATION][]',
                                            'label' => '合作银行',
                                            'rules' => 'xss_clean|trim'
                                         ),
                                    array(
                                            'field' => 'form_main[FINANCE_THIS]',
                                            'label' => '目前在外融资金额',
                                            'rules' => 'max_length[10]|numeric|xss_clean|trim'
                                         ),
                                    array(
                                            'field' => 'form[OWNER_HOME]',
                                            'label' => '企业主籍贯',
                                            'rules' => 'max_length[45]|xss_clean|trim'
                                         ),
                                    array(
                                            'field' => 'form[OWNER_TIME]',
                                            'label' => '企业主从业年限',
                                            'rules' => 'max_length[5]|numeric|xss_clean|trim'
                                         ),
                                    array(
                                            'field' => 'form[COM_TIME]',
                                            'label' => '企业经营年限',
                                            'rules' => 'max_length[5]|numeric|xss_clean|trim'
                                         ),
                                    array(
                                            'field' => 'form[STAFF]',
                                            'label' => '企业员工数',
                                            'rules' => 'max_length[10]|numeric|xss_clean|trim'
                                         ),
                                    array(
                                            'field' => 'form[ISLIST]',
                                            'label' => '是否有上市预期',
                                            'rules' => 'xss_clean|trim'
                                         ),
                                    array(
                                            'field' => 'form[BELONG]',
                                            'label' => '经营场所权属',
                                            'rules' => 'xss_clean|trim'
                                         ),
                                    array(
                                            'field' => 'form_main[ASSET_FIXED]',
                                            'label' => '固定资产',
                                            'rules' => 'max_length[10]|numeric|xss_clean|trim'
                                         ),
                                    array(
                                            'field' => 'form[ISRELATE]',
                                            'label' => '是否愿意连带责任',
                                            'rules' => 'xss_clean|trim'
                                         ),


                                    //business
                                    array(
                                            'field' => 'form[BRAND][about]',
                                            'label' => '代理品牌',
                                            'rules' => 'max_length[300]|xss_clean|trim'
                                         ),                                   
                                    array(
                                            'field' => 'form[BRAND][about2]',
                                            'label' => '代理商级别',
                                            'rules' => 'max_length[300]|xss_clean|trim'
                                         ),


                                    //science
                                    array(
                                            'field' => 'form[ISVARIOUS]',
                                            'label' => '是否为高新',
                                            'rules' => 'xss_clean|trim'
                                         ),
                                    array(
                                            'field' => 'form[TALLENT][]',
                                            'label' => '所属于人才计划',
                                            'rules' => 'xss_clean|trim'
                                         ),                        
                                    array(
                                            'field' => 'form[TALLENT][about]',
                                            'label' => '所属其他人才计划',
                                            'rules' => 'max_length[60]|xss_clean|trim'
                                         ),


                                    //farming
                                    array(
                                            'field' => 'form[V_TOTAL]',
                                            'label' => '村名下资产总量',
                                            'rules' => 'max_length[10]|numeric|xss_clean|trim'
                                         ),
                                    array(
                                            'field' => 'form[V_SALE_LAST]',
                                            'label' => '去年末村可支配收入',
                                            'rules' => 'max_length[10]|numeric|xss_clean|trim'
                                         ),
                                    array(
                                            'field' => 'form[V_SALE_BEFORE]',
                                            'label' => '前年末村可支配收入',
                                            'rules' => 'max_length[10]|numeric|xss_clean|trim'
                                         ),


                                    //cultural
                                    array(
                                            'field' => 'form[OWNER_TITLE]',
                                            'label' => '企业主职称',
                                            'rules' => 'max_length[135]|xss_clean|trim'
                                         ),

                                    array(
                                            'field' => 'form[TRADE_2]',
                                            'label' => '所属文化行业',
                                            'rules' => 'xss_clean|trim'
                                         ),
                                    array(
                                            'field' => 'form[COM_HONOR][about]',
                                            'label' => '企业荣誉',
                                            'rules' => 'max_length[300]|xss_clean|trim'
                                         ),



                                    //mid
                                    array(
                                            'field' => 'form[ASSET][]',
                                            'label' => '资产总额',
                                            'rules' => 'numeric|max_length[10]|xss_clean|trim'
                                         ),
                                    array(
                                            'field' => 'form[DEBT][]',
                                            'label' => '负债总额',
                                            'rules' => 'numeric|max_length[10]|xss_clean|trim'
                                         ),
                                    array(
                                            'field' => 'form[OWNER_RIGHTS][]',
                                            'label' => '所有者权益',
                                            'rules' => 'numeric|max_length[10]|xss_clean|trim'
                                         ),
                                    array(
                                            'field' => 'form[ACCOUNT_C][0]',
                                            'label' => '当前月报应收账款',
                                            'rules' => 'numeric|max_length[10]|xss_clean|trim'
                                         ),
                                    array(
                                            'field' => 'form[ACCOUNT_C][1]',
                                            'label' => '上年月报应收账款',
                                            'rules' => 'numeric|max_length[10]|xss_clean|trim'
                                         ),
                                    array(
                                            'field' => 'form[ACCOUNT_C][2]',
                                            'label' => '前年月报应收账款',
                                            'rules' => 'numeric|max_length[10]|xss_clean|trim'
                                         ),
                                    array(
                                            'field' => 'form[ACCOUNT_P][]',
                                            'label' => '应付账款',
                                            'rules' => 'numeric|max_length[10]|xss_clean|trim'
                                         ),
                                    array(
                                            'field' => 'form[STOCK][]',
                                            'label' => '存货余额以及补充说明',
                                            'rules' => 'xss_clean|trim'
                                         ),
                                    array(
                                            'field' => 'form[MAIN_SALE][]',
                                            'label' => '主营业务销售收入',
                                            'rules' => 'numeric|max_length[10]|xss_clean|trim'
                                         ),
                                    array(
                                            'field' => 'form[COST][]',
                                            'label' => '销售成本',
                                            'rules' => 'numeric|max_length[10]|xss_clean|trim'
                                         ),
                                    array(
                                            'field' => 'form[PROFIT][]',
                                            'label' => '净利润',
                                            'rules' => 'numeric|max_length[10]|xss_clean|trim'
                                         ),
                                    array(
                                            'field' => 'form[CASHFLOW][]',
                                            'label' => '经营性净现金流',
                                            'rules' => 'numeric|max_length[10]|xss_clean|trim'
                                         ),
                                    array(
                                            'field' => 'form[ACCOUNT_OC][0]',
                                            'label' => '当前月报其他应收款',
                                            'rules' => 'numeric|max_length[10]|xss_clean|trim'
                                         ),
                                    array(
                                            'field' => 'form[ACCOUNT_OC][1]',
                                            'label' => '上年月报其他应收款',
                                            'rules' => 'numeric|max_length[10]|xss_clean|trim'
                                         ),
                                    array(
                                            'field' => 'form[INVESTMENT][0]',
                                            'label' => '当前月报短期和长期投资',
                                            'rules' => 'numeric|max_length[10]|xss_clean|trim'
                                         ),
                                    array(
                                            'field' => 'form[INVESTMENT][1]',
                                            'label' => '上年月报短期和长期投资',
                                            'rules' => 'numeric|max_length[10]|xss_clean|trim'
                                         ),
                                    array(
                                            'field' => 'form[SHORT][0]',
                                            'label' => '当前月报银行短期借款',
                                            'rules' => 'numeric|max_length[10]|xss_clean|trim'
                                         ),
                                    array(
                                            'field' => 'form[SHORT][1]',
                                            'label' => '上年月报银行短期借款',
                                            'rules' => 'numeric|max_length[10]|xss_clean|trim'
                                         ),
                                    array(
                                            'field' => 'form[LONG][0]',
                                            'label' => '当前月报银行长期借款',
                                            'rules' => 'numeric|max_length[10]|xss_clean|trim'
                                         ),
                                    array(
                                            'field' => 'form[LONG][1]',
                                            'label' => '上年月报银行长期借款',
                                            'rules' => 'numeric|max_length[10]|xss_clean|trim'
                                         ),

                                    array(
                                            'field' => 'form[PAYABLE][0]',
                                            'label' => '当前月报应付票据',
                                            'rules' => 'numeric|max_length[10]|xss_clean|trim'
                                         ),
                                    array(
                                            'field' => 'form[PAYABLE][1]',
                                            'label' => '上年月报应付票据',
                                            'rules' => 'numeric|max_length[10]|xss_clean|trim'
                                         ),                                     

                                    array(
                                            'field' => 'form[ACCOUNT_OP][0]',
                                            'label' => '当前月报其他应付款',
                                            'rules' => 'numeric|max_length[10]|xss_clean|trim'
                                         ),
                                    array(
                                            'field' => 'form[ACCOUNT_OP][1]',
                                            'label' => '上年月报其他应付款',
                                            'rules' => 'numeric|max_length[10]|xss_clean|trim'
                                         ),  

                                    array(
                                            'field' => 'form[ASSET_FIXED][]',
                                            'label' => '固定资产',
                                            'rules' => 'numeric|max_length[10]|xss_clean|trim'
                                         ),

                                    array(
                                            'field' => 'form[MATERIAL][]',
                                            'label' => '存货余额中原材料',
                                            'rules' => 'numeric|max_length[10]|xss_clean|trim'
                                         ),
                                    array(
                                            'field' => 'form[FIELD_RIGHTS][]',
                                            'label' => '无形土地使用权',
                                            'rules' => 'max_length[10]|xss_clean|trim'
                                         ),
                                    array(
                                            'field' => 'form[ACCOUNT_OP_S][]',
                                            'label' => '其他应付款中股东借款',
                                            'rules' => 'numeric|max_length[10]|xss_clean|trim'
                                         ),
                                                                     
                                    array(
                                            'field' => 'form[OWNER_HOME]',
                                            'label' => '企业主籍贯',
                                            'rules' => 'max_length[60]|xss_clean|trim'
                                         ),
                                    array(
                                            'field' => 'form[OWNER_TIME]',
                                            'label' => '企业主从业年限',
                                            'rules' => 'xss_clean|trim'
                                         ),
                                    array(
                                            'field' => 'form[EXPERIENCE][0][]',
                                            'label' => '企业主从业经历',
                                            'rules' => 'max_length[200]|xss_clean|trim'
                                         ),
                                    array(
                                            'field' => 'form[CERTIFICATE][0][]',
                                            'label' => '资质证书',
                                            'rules' => 'max_length[200]|xss_clean|trim'
                                         ),
                                    array(
                                            'field' => 'form[COM_TIME]',
                                            'label' => '企业经营年限',
                                            'rules' => 'xss_clean|trim'
                                         ),
                                    array(
                                            'field' => 'form[STAFF]',
                                            'label' => '员工数',
                                            'rules' => 'xss_clean|trim'
                                         ),
                                    array(
                                            'field' => 'form[BELONG]',
                                            'label' => '经营场所权属',
                                            'rules' => 'xss_clean|trim'
                                         ),



                                    //补充说明
                                    array(
                                            'field' => 'form_main[PS][0]',
                                            'label' => '固定资产投入',
                                            'rules' => 'trim|numeric|max_length[10]|xss_clean'
                                         ),
                                    array(
                                            'field' => 'form_main[PS][1]',
                                            'label' => '新接订单',
                                            'rules' => 'trim|numeric|max_length[10]|xss_clean'
                                         ),
                                    array(
                                            'field' => 'form_main[PS][2]',
                                            'label' => '其他',
                                            'rules' => 'max_length[300]|xss_clean|trim'
                                         ),

                                    ),


                 'stepex' => array(
                                    // array(
                                    //         'field' => 'form_main[PS][0]',
                                    //         'label' => '固定资产投入',
                                    //         'rules' => 'required|numeric|max_length[10]|xss_clean|trim'
                                    //      ),
                                    // array(
                                    //         'field' => 'form_main[PS][1]',
                                    //         'label' => '新接订单',
                                    //         'rules' => 'required|numeric|max_length[10]|xss_clean|trim'
                                    //      ),
                                    array(
                                            'field' => 'form_main[PS][2]',
                                            'label' => '资金需求补充说明',
                                            'rules' => 'max_length[300]|xss_clean|trim'
                                         ),
                                    ),


                 'ts' => array(
                                    array(
                                            'field' => 'area[]',
                                            'label' => '担保方式123',
                                            'rules' => 'required|select_check[0]'
                                         ),

                                    array(
                                            'field' => 'ts1[]',
                                            'label' => '文本',
                                            'rules' => 'required|alpha_sub|max_length[5]|xss_clean|trim'
                                         ),


                                    )                          
               );






?>