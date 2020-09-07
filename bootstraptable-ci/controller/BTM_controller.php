<?php
class BTM_controller extends CI_Controller {

    public function index()//首页
    {
        
        $data['title'] = 'BTM教材订购系统';
        $data['username']='新用户';
    
        $this->load->view('btmviews/unlogin_header', $data);
        $this->load->view('btmviews/btm');
        $this->load->view('btmviews/footer');
    }

    public function student_login()//学生登陆后的跳转
    {
        $this->load->library('session');$this->load->helper('url');//虽然是自动加载的
        $this->load->model('BTM_model');

        if($this->BTM_model->student_loginsuccessful())
        //如果匹配成功，将用户信息保存至session，并跳转至商品浏览
        {
            $userid1= $this->input->post('studentlogin_userid');
            $user = array(
                'userid'=> $userid1,
                'usertype'=>'student'
            );
            $this->session->set_userdata('user',$user);//这里记录的是user
            redirect ('../student_class');
        }
        else 
        {
            echo "<script> alert('登陆失败') </script>";
            header("refresh:1;url=../../index.php/BTM_controller");
        }     
    }

    public function kuguan_login()//库管登陆后的跳转
    {
        $this->load->library('session');$this->load->helper('url');//虽然是自动加载的
        $this->load->model('BTM_model');

        if($this->BTM_model->kuguan_loginsuccessful())
        //如果匹配成功，将用户信息保存至session，并跳转至商品浏览
        {
            $userid1= $this->input->post('kuguanlogin_userid');
            $user = array(
                'userid'=> $userid1,
                'usertype'=>'kuguan'
            );
            $this->session->set_userdata('user',$user);//这里记录的是user
            redirect ('../kuguan_index');
        }
        else 
        {
            echo "<script> alert('登陆失败') </script>";
            header("refresh:1;url=../BTM_controller");
        }   
    }


    public function student_class()//学生课程-书籍对应关系
    {
        $this->load->library('session');$this->load->helper('url');
        $user = $this->session->userdata('user'); 
        // 每个页面都要加载一下session
              
        $this->load->model('BTM_model');

        $username=$this->BTM_model->get_student_username($user['userid']);

        $data['title']='我的课程';
        $data['username']=$username;
        
        $data['bookcourseinfo']=$this->BTM_model->get_bookcourseinfo($user['userid']); 
        //获取一下课程之类的
        if(isset($_SESSION['user'])) 
        {          
            if($user['usertype']=='student')
            {
                $this->load->view('btmviews/student_header',$data);
                $this->load->view('btmviews/student_class',$data);
                $this->load->view('btmviews/footer');
            }
            else 
            {
                echo "<script> 
                    alert(\"对不起，您没有学生的权限。\");
                 </script>";
                 header("refresh:1;url=../btm_controller/index");
            }
        }
        else 
        {
            echo "<script> 
                    alert(\"你需要先登陆才能访问该页面\");
                 </script>";
            header("refresh:1;url=../btm_controller/index");
            //1秒后自动跳转至主页
        }
        
    }

    public function get_student_order()//获取学生订单
    {

        $this->load->library('session');$this->load->helper('url');
        $user = $this->session->userdata('user');
        // 每个页面都要加载一下session

        $this->load->model('BTM_model');
        $username = $this->BTM_model->get_student_username($user['userid']);
        $data['username']=$username;
        $data['title']='已订教材';
        $data['orderinfo'] = $this->BTM_model->get_student_order($user['userid']);
        if(isset($_SESSION['user']))
        {
            if($user['usertype']=='student')
            {
            $this->load->view('btmviews/student_header',$data);
            $this->load->view('btmviews/student_order',$data);
            $this->load->view('btmviews/footer');
            }
            else 
            {
                echo "<script> 
                    alert(\"对不起，您没有学生的权限。\");
                 </script>";
                 header("refresh:1;url=../btm_controller/index");
            }
        }
        else
        {
            echo "<script> 
                    alert(\"你需要先登陆才能访问该页面\");
                 </script>";
            header("refresh:1;url=../btm_controller/index");
            //1秒后自动跳转至主页
        }
    }

    public function get_student_draw()//学生获取领书信息
    {

        $this->load->library('session');$this->load->helper('url');
        $user = $this->session->userdata('user');
        // 每个页面都要加载一下session

        $this->load->model('BTM_model');
        $username = $this->BTM_model->get_student_username($user['userid']);
        $data['username']=$username;
        $data['title']='我的领书单';
        $data['drawinfo'] = $this->BTM_model->get_student_draw($user['userid']);
        if(isset($_SESSION['user']))
        {
            if($user['usertype']=='student')
            {
            $this->load->view('btmviews/student_header',$data);
            $this->load->view('btmviews/student_draw',$data);
            $this->load->view('btmviews/footer');
            }
            else 
            {
                echo "<script> 
                    alert(\"对不起，您没有学生的权限。\");
                 </script>";
                 header("refresh:1;url=../btm_controller/index");
            }
        }
        else
        {
            echo "<script> 
                    alert(\"你需要先登陆才能访问该页面\");
                 </script>";
            header("refresh:1;url=../btm_controller/index");
            //1秒后自动跳转至主页
        }
    }

     //1获取所有订书单
     public function kuguan_index()
     {
         $this->load->library('session');$this->load->helper('url');
         $user = $this->session->userdata('user'); 
         $this->load->model('BTM_model');
 
         $data['title']='订书单列表';
         $data['username']=$user['userid'];
         $data['dingshudan']=$this->BTM_model->get_dingshudan(); 
 
         if(isset($_SESSION['user'])) 
         {   
             if($user['usertype']=='kuguan')  
             {     
             $this->load->view('btmviews/kuguan_header',$data);
             $this->load->view('btmviews/kuguan_index',$data);
             $this->load->view('btmviews/footer');
             }
             else 
             {
                 echo "<script> 
                     alert(\"对不起，您没有库存管理员的权限。\");
                  </script>";
                  header("refresh:1;url=../btm_controller/index");
             }
         }
         else 
         {
             echo "<script> 
                     alert(\"你需要先登陆才能访问该页面\");
                  </script>";
             header("refresh:1;url=../btm_controller/index");
             //1秒后自动跳转至主页
         }
         
     }

    public function add_order()//还没改好，增加订单
    {
        $this->load->library('session');$this->load->helper('url');
        $user = $this->session->userdata('user'); 
        // 每个页面都要加载一下session
        $userid=$user['userid'];
        //找出userid 方便一会儿存入数据库
        $adddate=date('Y-m-d h:i:s', time()+8*3600);;
        $count=0;
        $count2=0;

        $TB_isbn_array=$this->input->post('order_isbn[]');
        // $Ord_Quantity_array=$this->input->post('order_count[]');
        $Ord_Checkbox_array=$this->input->post('order_checkbox[]');

        
        foreach ($_POST as $key=>$value) {
            if (($key=='order_isbn')||($key=='order_checkbox'))
             { continue; }
            else
            {
                $Ord_Quantity_array[]=$value;
                // $count2=$count2+1;
            }
        }


        while(($count <= sizeof($TB_isbn_array)-1))
        {
            if($Ord_Checkbox_array[$count]==1)
            {
                $order_isbn=$TB_isbn_array[$count];
                $order_count=$Ord_Quantity_array[$count/2];
                // $order_count=$this->input->post('order_count{$count}');
                echo $order_count;
                echo $order_isbn;
                $inv_query = "INSERT INTO OrderInfo (TB_isbn,Stu_No,Ord_Quantity,Ord_date,Is_Draw) VALUES 
                            ('$order_isbn','$userid', '$order_count','$adddate',0)";
                $this->db->query($inv_query);
            }
            $count++;
            // $count2=$count2+1/2;
        }     
        redirect ('../student_class');
    }

    public function kuguan_lingshudan()//库管获取领书信息
    {
        $this->load->library('session');$this->load->helper('url');
        $user = $this->session->userdata('user'); 
        // 每个页面都要加载一下session
              
        $this->load->model('BTM_model');

        $data['title']='我的领书单';
        $data['username']=$user['userid'];
        
        $data['my_lingshudan']=$this->BTM_model->get_mylingshudan($user['userid']); 
        //获取一下自己负责的领书单

        if(isset($_SESSION['user'])) 
        {   
            if($user['usertype']=='kuguan')  
            {     
            $this->load->view('btmviews/kuguan_header',$data);
            $this->load->view('btmviews/kuguan_lingshudan',$data);
            $this->load->view('btmviews/footer');
            }
            else 
            {
                echo "<script> 
                    alert(\"对不起，您没有库存管理员的权限。\");
                 </script>";
                 header("refresh:1;url=../btm_controller/index");
            }
        }
        else 
        {
            echo "<script> 
                    alert(\"你需要先登陆才能访问该页面\");
                 </script>";
            header("refresh:1;url=../btm_controller/index");
            //1秒后自动跳转至主页
        }
        
    }

      //2查看已认领订书单
      public function ckdsd($Ord_id=NULL)
      {
          //每个页面都要加载session
          $this->load->model('BTM_model');
          $this->load->library('session');$this->load->helper('url');
          $user = $this->session->userdata('user'); 
          $data['username']=$user['userid'];
  
          //模型
          $data['shenqing'] = $this->BTM_model->ckdsd($data['username']);//对于user的所有申请
          $data['shenqing_item'] = $this->BTM_model->ckdsd($data['username'],$Ord_id);//id查到的某个申请
  
          
          $data['title'] = '负责订单';
          // $data['shangpinmingcheng'] = $data['shoucang_item']['shangpinmingcheng'];
          // $data['addtime'] = $data['shoucang_item']['addtime'];
          // $data['shoucang_id'] = $data['shoucang_item']['shoucang_id'];
          if(isset($_SESSION['user'])) 
          {   
              if($user['usertype']=='kuguan')  
              { 
                 $this->load->view('btmviews/kuguan_header', $data);
                 $this->load->view('btmviews/kuguan_ckdsd', $data);
                 $this->load->view('btmviews/footer');
                }
                else 
                {
                    echo "<script> 
                        alert(\"对不起，您没有库存管理员的权限。\");
                     </script>";
                     header("refresh:1;url=../btm_controller/index");
                }
            }
            else 
            {
                echo "<script> 
                        alert(\"你需要先登陆才能访问该页面\");
                     </script>";
                header("refresh:1;url=../btm_controller/index");
                //1秒后自动跳转至主页
            }
      }
  
 
      //2.1修改库存并发放领书单 页面
    public function kucun_modify() {
        $Ord_id = $this->input->get('Ord_id');
        $this->load->model('BTM_model');
        $this->load->library('session');
        $this->load->helper('url');
        $user = $this->session->userdata('user');
        $data = $this->BTM_model->ckdsdInfo($Ord_id);
        $data['title'] = '修改库存';
        $data['username'] = $user['userid'];
        if(isset($_SESSION['user'])) 
        {   
            if($user['usertype']=='kuguan')  
            { 
                $this->load->view('btmviews/kuguan_header', $data);
                $this->load->view('btmviews/kucun_modify', $data);
                $this->load->view('btmviews/footer');
            }
            else 
            {
                echo "<script> 
                    alert(\"对不起，您没有库存管理员的权限。\");
                 </script>";
                 header("refresh:1;url=../btm_controller/index");
            }
        }
        else 
        {
            echo "<script> 
                    alert(\"你需要先登陆才能访问该页面\");
                 </script>";
            header("refresh:1;url=../btm_controller/index");
            //1秒后自动跳转至主页
        }
    }
 
  //修改库存 功能
  public function modify_inventory() {       
    $Ord_id = $this->input->post('Ord_id');
    $TB_isbn = $this->input->post('TB_isbn');
    $TB_Inventory = $this->input->post('TB_Inventory');
    $this->load->model('BTM_model');
    $this->load->library('session');
    $this->load->helper('url');
    $user = $this->session->userdata('user');
    $data['username'] = $user['userid'];
    $result = $this->BTM_model->modify_inventory($Ord_id, $TB_isbn, $TB_Inventory);
    if ($result) {
    self::ckdsd();
    }

    }

    //认领订书单 功能
    // public function rldsd() {
        public function rldsd() {
            // $Ord_id = $this->input->post('Ord_id');
            $this->load->model('BTM_model');
            $this->load->library('session');
            $this->load->helper('url');
            $user = $this->session->userdata('user');
            $data['username'] = $user['userid'];
            $Ord_id_thisrow=$this->uri->segment(3);
            // echo $Ord_id_thisrow;
            // $result = $this->BTM_model->rldsd($Ord_id,$user);
            $result = $this->BTM_model->rldsd_model($Ord_id_thisrow,$user);
            // if ($result) {
            // self::kuguan_index();
            // }
            redirect ('../kuguan_index');
        
        }

        public function get_shoppingmall()//获取商城
    {

        $this->load->library('session');
        $this->load->helper('url');
        $this->load->library('pagination');//分页

        $user = $this->session->userdata('user');
        // 每个页面都要加载一下session

        $this->load->model('BTM_model');
        $username = $this->BTM_model->get_student_username($user['userid']);
        $data['username']=$username;
        $data['title']='教材商城';
        $shangpin_count = $this->BTM_model->get_shangpin_allNums();//获取数据数量
        $limit_per_page = 2;

        

        if(isset($_SESSION['user']))
        {
            if($user['usertype']=='student')
            {
                
                $config['base_url'] = 'get_shoppingmall';//这是一个指向你的分页所在的控制器类/方法的完整的URL
                
                
                $config['total_rows'] = $shangpin_count;//这个数字表示你需要做分页的数据的总行数
                $config['per_page'] = $limit_per_page;//这个数字表示每个页面中希望展示的数量
                $config['first_link'] = '首页';
                $config['prev_link'] = '上一页';
                $config['next_link'] = '下一页';
                $config['last_link'] = '末页';
                $config['uri_segment'] = 3;//必须与$this->uri->segment(3)保持一致
/*
bootstrap 风格的分页样式
*/
$config['full_tag_open'] = '<nav aria-label="Page navigation example"><ul class="pagination">'; 
$config['full_tag_close'] = '</ul></nav>';

$config['first_tag_open'] = '<li class="page-item page-link">'; 
$config['first_tag_close'] = '</li>';

$config['prev_tag_open'] = '<li class="page-item page-link">'; 
$config['prev_tag_close'] = '</li>';

$config['cur_tag_open'] = '<li class="active page-item page-link"> '; 
$config['cur_tag_close'] = '</li>';

$config['num_tag_open'] = '<li class="page-item page-link">'; 
$config['num_tag_close'] = '</li>';
$config['next_tag_open'] = '<li class="page-item page-link"';
$config['next_tag_close'] = '</li>';
$config['last_tag_open'] = '<li class="page-item page-link" >'; 
$config['last_tag_close'] = '</li>';
               
 
                $this->pagination->initialize($config);

                $start_index = intval($this->uri->segment(3));
                $data['current_items'] = $this->BTM_model->get_current_page_records($limit_per_page, $start_index);
                //获取当前页的数据

                $data['links'] = $this->pagination->create_links();  //当你没有分页需要显示时，create_links() 方法会返回一个空的字符串。
                
                $this->pagination->initialize($config);

                $search_content = $this->input->post('shoppingmallsearch');
                $search_type = $this->input->post('shoppingmallradio');

                 $this->load->view('btmviews/student_header',$data);
                 $this->load->view('btmviews/student_shoppingmall',$data);
                
                //  $this->load->view('btmviews/testshoppingmall',$data);
                 $this->load->view('btmviews/footer');
            }
            else 
            {
                echo "<script> 
                    alert(\"对不起，您没有学生的权限。\");
                 </script>";
                 header("refresh:1;url=../btm_controller/index");
            }
        }
        else
        {
            echo "<script> 
                    alert(\"你需要先登陆才能访问该页面\");
                 </script>";
            header("refresh:1;url=../btm_controller/index");
            //1秒后自动跳转至主页
        }
    }

    public function get_shoppingmall_search()//获取商城
    {

        $this->load->library('session');
        $this->load->helper('url');
        $this->load->library('pagination');//分页

        $user = $this->session->userdata('user');
        // 每个页面都要加载一下session

        $this->load->model('BTM_model');
        $username = $this->BTM_model->get_student_username($user['userid']);
        $data['username']=$username;
        $data['title']='搜索结果';
        $shangpin_count = $this->BTM_model->get_shangpin_allNums();//获取数据数量
        $limit_per_page = 2;

        if(isset($_SESSION['user']))
        {
            if($user['usertype']=='student')
            {
                
                $config['base_url'] = 'get_shoppingmall_search';//这是一个指向你的分页所在的控制器类/方法的完整的URL
                
                
                $config['total_rows'] = $shangpin_count;//这个数字表示你需要做分页的数据的总行数
                $config['per_page'] = $limit_per_page;//这个数字表示每个页面中希望展示的数量
                $config['first_link'] = '首页';
                $config['prev_link'] = '上一页';
                $config['next_link'] = '下一页';
                $config['last_link'] = '末页';
                $config['uri_segment'] = 3;//必须与$this->uri->segment(3)保持一致
/*
bootstrap 风格的分页样式
*/
$config['full_tag_open'] = '<nav aria-label="Page navigation example"><ul class="pagination">'; 
$config['full_tag_close'] = '</ul></nav>';

$config['first_tag_open'] = '<li class="page-item page-link">'; 
$config['first_tag_close'] = '</li>';

$config['prev_tag_open'] = '<li class="page-item page-link">'; 
$config['prev_tag_close'] = '</li>';

$config['cur_tag_open'] = '<li class="active page-item  page-link"> '; 
$config['cur_tag_close'] = '</li>';

$config['num_tag_open'] = '<li class="page-item page-link">'; 
$config['num_tag_close'] = '</li>';
$config['next_tag_open'] = '<li class="page-item page-link"';
$config['next_tag_close'] = '</li>';
$config['last_tag_open'] = '<li class="page-item page-link" >'; 
$config['last_tag_close'] = '</li>';
               
                $search_content = $this->input->post('shoppingmallsearch');
                $search_type = $this->input->post('shoppingmallradio');

                $this->pagination->initialize($config);

                $start_index = intval($this->uri->segment(3));
                $data['current_items'] = $this->BTM_model->get_current_page_records_search($limit_per_page, $start_index,$search_content,$search_type);
                //获取当前页的数据

                $data['links'] = $this->pagination->create_links();  //当你没有分页需要显示时，create_links() 方法会返回一个空的字符串。
                
                $this->pagination->initialize($config);

                 $this->load->view('btmviews/student_header',$data);
                 $this->load->view('btmviews/student_shoppingmall',$data);
        
                //  $this->load->view('btmviews/testshoppingmall',$data);
                 $this->load->view('btmviews/footer');
            }
            else 
            {
                echo "<script> 
                    alert(\"对不起，您没有学生的权限。\");
                 </script>";
                 header("refresh:1;url=../btm_controller/index");
            }
        }
        else
        {
            echo "<script> 
                    alert(\"你需要先登陆才能访问该页面\");
                 </script>";
            header("refresh:1;url=../btm_controller/index");
            //1秒后自动跳转至主页
        }
    }

    public function get_select_book($TB_isbn)//获取书籍详情
    {

        $this->load->library('session');
        $this->load->helper('url');
        $user = $this->session->userdata('user');
        // 每个页面都要加载一下session

        $this->load->model('BTM_model');
        $username = $this->BTM_model->get_student_username($user['userid']);
        $data['username']=$username;
        $data['title']='书籍详情';
        $data['booksinfo'] = $this->BTM_model->get_booksinfo($TB_isbn);//获取书籍信息
        $data['bookscourseinfo'] = $this->BTM_model->get_bookscourseinfo($TB_isbn);//获取书籍-课程信息


        if(isset($_SESSION['user']))
        {
            if($user['usertype']=='student')
            {
            $this->load->view('btmviews/student_header',$data);
            $this->load->view('btmviews/shangpinxiangqing',$data);
            $this->load->view('btmviews/footer');
            }
            else 
            {
                echo "<script> 
                    alert(\"对不起，您没有学生的权限。\");
                 </script>";
                 header("refresh:1;url=../btm_controller/index");
            }
        }
        else
        {
            echo "<script> 
                    alert(\"你需要先登陆才能访问该页面\");
                 </script>";
            header("refresh:1;url=../btm_controller/index");
            //1秒后自动跳转至主页
        }
    }

    function kuguan_kucun_manage()
    {
        $this->load->view('btmviews/kuguan_kucunguanli');
    }

    public function kuguan_kcgl()//库管获取领书信息
    {
        $this->load->library('session');$this->load->helper('url');
        $user = $this->session->userdata('user'); 
        // 每个页面都要加载一下session
              
        $this->load->model('BTM_model');

        $data['title']='库存信息';
        $data['username']=$user['userid'];
        
        // $data['kucun']=$this->BTM_model->get_kuncunyemian(); 

        $kucun1=$this->BTM_model->get_kuncunyemian(); 
        $kucun2=json_encode($kucun1);
        $kucun3='['.$kucun2.']';

        file_put_contents('/Library/WebServer/Documents/BTMSystem/application/json/test.json',$kucun2);
        if(isset($_SESSION['user'])) 
        {   
            if($user['usertype']=='kuguan')  
            {     
            $this->load->view('btmviews/kuguan_header',$data);
            $this->load->view('btmviews/kuguan_kucunguanli',$data);
            $this->load->view('btmviews/footer');
            }
            else 
            {
                echo "<script> 
                    alert(\"对不起，您没有库存管理员的权限。\");
                 </script>";
                 header("refresh:1;url=../btm_controller/index");
            }
        }
        else 
        {
            echo "<script> 
                    alert(\"你需要先登陆才能访问该页面\");
                 </script>";
            header("refresh:1;url=../btm_controller/index");
            //1秒后自动跳转至主页
        }
        
    }

    public function get_kucunjson()
    {     
        $this->load->model('BTM_model');
        $kucun = $this->BTM_model->get_kuncunyemian();
        // echo json_encode($kucun);
        // $this->load->model('BTM_model');
        // $kucun = $this->BTM_model->get_kuncunyemian();
        // echo json_encode($kucun);
        return json_encode($kucun);
    }
 
   
 

   


    
   

}   