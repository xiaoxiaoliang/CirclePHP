<?php
class Page{
    public $firstRow; // 起始行数
    public $listRows; // 列表每页显示行数
    public $totalRows; // 总行数
    public $totalPages; // 分页总页面数
    private $nowPage = 1;//当前页数

    public function __construct($totalRows, $listRows=20, $nowPage = 1) {
        /* 基础设置 */
        $this->totalRows  = $totalRows; //设置总记录数
        $this->listRows   = $listRows;  //设置每页显示行数
		$this->totalPages = ceil($totalRows/$listRows);
        $this->nowPage    = $nowPage ? intval($nowPage) : 1;//设置当前页
        $this->nowPage    = $this->nowPage>0 ? $this->nowPage : 1;//过滤当前页
        $this->nowPage    = $this->nowPage<=$this->totalPages ? $this->nowPage : $this->totalPages;//过滤当前页
        $this->firstRow   = $this->listRows * ($this->nowPage - 1);//获取第一行
    }
    public function pageList() {
    	//$prePage = $this->nowPage - 1;
    	$str = '<li ';
    	if($this->nowPage != 1) {
    		$str .= '><a href="javascript:void(0);" val="1">&laquo;</a></li>';
    	} else {
    		$str .= 'class="disabled" ><a href="javascript:void(0);">&laquo;</a></li>';
    	}
    	
    	$startPage = $this->nowPage - 3;
    	$endPage = $this->nowPage + 3;
    	for ($i = $startPage; $i <= $endPage; ++$i) {
    		if($i>0 && $i <= $this->totalPages) {
    			if($i != $this->nowPage) {
    				$str.='<li><a href="javascript:void(0);" val="'.$i.'">'.$i.'</a></li>';
    			} else {
    				$str.='<li class="active"><a href="javascript:void(0);">'.$i.' <span class="sr-only">(current)</span></a></li>';
    			}
    		}
    	}
    	
    	//$nextPage = $this->nowPage + 1;
    	if($this->nowPage != $this->totalPages) {
    		$str .= '<li><a href="javascript:void(0);" val="'.$this->totalPages.'">&raquo;</a></li>';
    	} else {
    		$str .= '<li class="disabled"><a href="javascript:void(0);">&raquo;</a></li>';
    	}

		return $str;
    }

    public function getNowPage(){
    	 return $this->nowPage;
    }
    
}
