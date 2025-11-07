<?php
// 本类由系统自动生成，仅供测试用途
namespace Home\Controller;
use Think\Controller;
class IndexController extends CommonController {

    public function testpre(){
        pre(date('Y-m-d H:i','1578319204'));
        pre(S('test1'));
        pre(S('post'));
        pre(S('content'));
        pre(S('imgnames'));

    }

    public function index(){
        $cat = new \Org\Util\Category();
        // 本站头条【
        if (!S(C('CACHE_FIX').'_newtop')) {
            $category = M("Category");
            $newtop['cname'] = $category->where(array('cid'=>2))->getField('name');
            $cate = $category->order('sort desc')->select();
            $cidarr = $cat->getChildsId($cate,2);
            array_unshift($cidarr,2);//包含主分类下的新闻
            $newtop['list'] = D('News')->where(array('cid'=>array('in',$cidarr),'status'=>1))->limit(6)->field('id,title,name')->order('published desc')->select();
            // 后台添加文章或缓存过期会删除该缓存
            S(C('CACHE_FIX').'_newtop',$newtop,7200);
        }else{
            $newtop = S(C('CACHE_FIX').'_newtop');
        }
        $this->newtop=$newtop;
        // 本站头条】
        $uid = $this->cUid;
        if ($uid) {
            // 用户订单状态显示【
            $order = M('Goods_order');
            $orderlist = array();
            $mywhere = array('uid'=>$uid);
            // 待付款
            $fukuai = $mywhere;
            $fukuai['status'] = 0;
            $fukuai['deftime1st'] = 0;
            $ocount['fukuai']=$order->where($fukuai)->count();
            // 待发货
            $fahuo = $mywhere;
            $fahuo['status'] = 1;
            $fahuo['deftime2st'] = 0;
            $ocount['fahuo']=$order->where($fahuo)->count();
            // 待收货
            $shouhuo = $mywhere;
            $shouhuo['status'] = 2;
            $shouhuo['deftime3st'] = 0;
            $ocount['shouhuo']=$order->where($shouhuo)->count();
            // 待评价
            $pingjia = $mywhere;
            $pingjia['status'] = 3;
            $pingjia['deftime4st'] = 0;
            $ocount['pingjia']=$order->where($pingjia)->count();
            $this->ocount=$ocount;
            // 用户订单状态显示】

            // 我的足迹【
            $goods = M('goods');
            $pidstr = M('member_footprint')->where(array('uid'=>$uid))->getField('pidstr');
            $footprint = array();
            $pidarr = $this->safeUnserializeArray($pidstr);
            if ($pidarr) {
                // 删除重复浏览的拍品，同时过滤非法ID
                $deduplicatedMap = array();
                foreach ($pidarr as $pidItem) {
                    if (!is_scalar($pidItem)) {
                        continue;
                    }

                    $pidItem = (int)$pidItem;
                    if ($pidItem <= 0) {
                        continue;
                    }

                    if (isset($deduplicatedMap[$pidItem])) {
                        continue;
                    }

                    $deduplicatedMap[$pidItem] = $pidItem;
                }

                $deduplicated = array_values($deduplicatedMap);

                // 只保留最近的50条足迹（原始顺序中靠后的认为是最新的记录）
                if (count($deduplicated) > 50) {
                    $deduplicated = array_slice($deduplicated, -50, 50);
                }

                if ($deduplicated) {
                    $pidOrder = implode(',', $deduplicated);
                    $auctionWhere = array(
                        'hide' => 0,
                        'pid'  => array('in', $deduplicated),
                    );

                    $footprint = M('Auction')
                        ->where($auctionWhere)
                        ->order("field(pid,".$pidOrder.")")
                        ->field('pid,gid,nowprice')
                        ->limit(50)
                        ->select();

                    foreach ($footprint as $ftkey => $ftvl) {
                        $footprint[$ftkey]['pictures']=$goods->where(array('id'=>$ftvl['gid']))->getField('pictures');
                    }
                }
            }
            $this->footprint=$footprint;
            // 我的足迹】
        }
        

        // 不显示被屏蔽用户的拍品、专场、拍卖会
        $notinUid = array();
        if ($xUidarr = blackuser($uid)) {
            $notinUid = array('sellerid'=>array('not in',$xUidarr));
        }
        // 开始的拍品、专场、拍卖会条件
        $stWhere = bidSection();
        // 专场、拍卖会内拍品必须大于0才显示
        $bcount = array('bcount'=>array('gt',0));
        // 专场拍卖【
        $special = M('special_auction');
        $slist = M('special_auction')->where(array_merge($stWhere,$notinUid,$bcount))->limit(3)->select();
        $this->slist=$slist;
        // 专场拍卖】
        // 拍卖会【
        $ftWhere = array(
            'starttime'=>array('lt',time()),
            'endtime'=>array('gt',time())
            );
        $meeting = M('meeting_auction');
        $mlist = M('meeting_auction')->where(array_merge($ftWhere,$notinUid,$bcount))->limit(3)->select();
        $this->mlist=$mlist;
        // 拍卖会】
        // 默认显示的拍品数量
        $limit = 12;
        // 显示的字段
        $field = 'pid,pname,nowprice,clcount,pictures,onset,bidcount';
        // 频道板块【
        $gCategory = M("goods_category");
        $auction = D('Auction');
        $gCate = $gCategory->order('sort desc')->select();
        $channel = $gCategory->where(array('pid'=>0,'hot'=>1))->field('cid,name,remark,modelno')->order('sort desc')->select();
        foreach ($channel as $ck => $cv) {
            // 获取需要查询的CID集合
            $gCidarr = $cat->getChildsId($gCate,$cv['cid']);
            //获取分类下推荐分类
            $channel[$ck]['hotcate'] = $gCategory->where(array('cid'=>array('in',$gCidarr),'hot'=>1))->field('cid,name')->select();

            array_unshift($gCidarr,$cv['cid']);//包含主分类
            $inCid = array('cid'=>array('in',$gCidarr));
            // 组合条件
            $tWhere = array_merge($stWhere,$notinUid,$inCid);
            $tCount = $auction->where($tWhere)->count();
            $channel[$ck]['count'] = $tCount;
            if ($cv['modelno'] == 1) {
                // 效果一必须大于7个商品
                if ($tCount>=7) {$channel[$ck]['modelno'] =1;$limit = 7;}else{$channel[$ck]['modelno'] =0;}
            }elseif ($cv['modelno'] == 2) {
                // 效果一必须大于9个商品
                if ($tCount>=9) {$channel[$ck]['modelno'] =2;$limit = 9;}else{$channel[$ck]['modelno'] =0;}
            }
            // 按照结束时间倒叙获取该频道需要显示商品
            $channel[$ck]['list'] = $auction->where($tWhere)->where($notinUid)->field($field)->limit($limit)->order('endtime desc')->select();
        }

        $this->channel = $channel;
        // 频道板块】

        // 即将开拍【
        $fuTime = foreshow(0);
        $fuWhere = array_merge($notinUid,$fuTime);
        $fulist = D('Auction')->where($fuWhere)->order('starttime desc')->field($field)->limit($limit)->select();
        $this->fulist=$fulist;
        // 即将开拍】

        //最近成交拍品列表处理-----------------------------------------------start
        $edTime = endbid(0);
        $edWhere = array_merge($notinUid,$edTime);
        $edlist = D('Auction')->where($edWhere)->order('endtime desc')->field($field)->limit($limit)->select();
        $this->edlist=$edlist;
        //最近成交拍品列表处理-----------------------------------------------end
        // 友情链接
        $link = M('link');
        $lklist = array(
            'txt'=>$link->order('sort desc')->where('rec = 0')->select(),
            'pic'=> $link->order('sort desc')->where('rec = 1')->select()
            );
        $this->lklist=$lklist;
        // 显示全部分类
        $this->cateshow=1;
        // 计算小导航限时数量
        $sm_avg = 2; 
        if (C('Auction.sort_time') == 1) {$sm_avg +=1;}
        if (C('Auction.yikoujia') == 1) {$sm_avg +=1;}
        if (C('Auction.jianlou') == 1) {$sm_avg +=1;}
        if (C('Auction.special_page') == 1) {$sm_avg +=1;}
        if (C('Auction.meeting_page') == 1) {$sm_avg +=1;}
        $this->sm_avg=$sm_avg;
        $this->display();
     }
     // 商品搜索
     public function search(){
        $auction = D('Auction');
        // 拍品条件
        $keywords = I('get.keywords');
        $type = I('get.type');
        $conplex['pname'] = array('LIKE', '%' . $keywords . '%');
        $conplex['keywords'] = array('LIKE', '%' . $keywords . '%');
        $conplex['_logic'] = 'or';
        if (in_array($type, array('biding','bidend','future'))) {
            $ws = bidType($type);
            $listwhere = $ws['bidType'];
        }
        $listwhere['hide'] = array('eq',0);
        $listwhere['_complex'] = $conplex;
        if ($xUidarr = blackuser($this->cUid)) {
            $listwhere['sellerid'] = array('not in',$xUidarr);
        }
        if (I('get.cj')) {
            $listwhere['endstatus'] = 1;
            $this->cj = 1;
        }
        switch ($type) {
            case 'biding':
                $od = 'endtime';
                break;
            case 'bidend':
                $od = 'endtime desc';
                break;
            case 'future':
                $od = 'starttime asc';
                break;
            
            default:
                $od = 'starttime desc';
                break;
        }
        $rdata = D('Auction')->auctionList($listwhere,$od,C('PAGE_SIZE'));
        $this->page = $rdata['page']; 
        // 分配正在拍卖拍品到模板
        $this->list = $rdata['list'];
        $this->saytyp = array('get'=>$type,'keywords'=>$keywords);
        $this->display();
     }
    public function iealert(){
        $this->display();
    }
    public function cate(){
        $this->cateshow=1;
        $this->catepage=1;
        $this->display();
    }
    public function more(){
        $this->display();
    }

    public function testa(){
        $url = U('Index/testb','',true);
        $datas = array('gol'=>1,'openid'=>'123456','access_token'=>'654321','create'=>'auto');
        pre(sendPost($url, $datas));
    }


    private function safeUnserializeArray($value)
    {
        if (!is_string($value) || $value === '') {
            return array();
        }

        $data = false;
        set_error_handler(function () {
            return true;
        });

        try {
            $data = unserialize($value);
        } catch (\Exception $exception) {
            $data = false;
        }

        restore_error_handler();

        if (!is_array($data)) {
            return array();
        }

        return $data;
    }


}