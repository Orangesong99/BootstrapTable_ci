<?php
class BTM_model extends CI_Model {

    public function student_loginsuccessful()
    {
        $id = $this->input->post ( 'studentlogin_userid' );
        $password = $this->input->post ( 'studentlogin_password' );

        $this->db->select('Stu_Password');
        $this->db->from('Students');
        $this->db->where('Stu_No', $id);

        $query= $this->db->get();
        $row = $query->row_array();
        $select_password = $row['Stu_Password'];
        
        if($select_password==$password)
        {
            return true;
        }
        else return false;

    }

    public function get_student_username($userid)
    {
        $this->db->select('Stu_Name');
        $this->db->from('Students');
        $this->db->where('Stu_No', $userid);

        $query= $this->db->get();
        $row = $query->row_array();
        $username = $row['Stu_Name'];
        
         return $username;

    }

    public function kuguan_loginsuccessful()
    {
        $id = $this->input->post ( 'kuguanlogin_userid' );
        $password = $this->input->post ( 'kuguanlogin_password' );

        $this->db->select('Im_Password');
        $this->db->from('InventoryManagerInfo');
        $this->db->where('Im_No', $id);

        $query= $this->db->get();
        $row = $query->row_array();
        $select_password = $row['Im_Password'];
        
        if($select_password==$password)
        {
            return true;
        }
        else return false;
    }  

    public function get_bookcourseinfo($userid)
    {
        $sql=
        "
        SELECT CourseInfo.Cou_No,CourseInfo.Cou_Name,Teachers.T_Name,TestBooksinfo.TB_Name,TestBooksinfo.TB_Price,TestBooksinfo.TB_isbn
        FROM TestBooksinfo
        JOIN CourseTestBookInfo ON CourseTestBookInfo.TB_isbn = TestBooksinfo.TB_isbn
        JOIN CourseInfo ON CourseInfo.Cou_No = CourseTestBookInfo.Cou_No
        JOIN Teachers ON Teachers.T_No = CourseInfo.T_No
        JOIN CourseClassInfo ON CourseClassInfo.Cou_No = CourseInfo.Cou_No
        JOIN Students ON Students.Cla_No = CourseClassInfo.Cla_No
        WHERE Stu_No = ?";
        return $this->db->query($sql,$userid);
    }

    public function get_mylingshudan($userid)
    {
        $sql=
        "
        SELECT DrawInfo.D_id,TestBooksInfo.TB_Name,OrderInfo.Ord_Quantity,OrderInfo.Stu_No,DrawInfo.D_date
        FROM DrawInfo
        JOIN OrderInfo ON DrawInfo.Ord_id = OrderInfo.Ord_id
        JOIN TestBooksInfo ON TestBooksInfo.TB_isbn = OrderInfo.TB_isbn
        WHERE Im_No = ?";
        return $this->db->query($sql,$userid);
    }

    public function get_student_order($userid)
    {
        $this->db->select('ord_id,tb_isbn,stu_no,ord_quantity,ord_date,im_no,is_draw');
        //这里有问题！
        $this->db->from('orderinfo');
        $this->db->where('Stu_No', $userid);
        $query = $this->db->get();
        return $query->result_array();
        //$ord = $row['ord_id,tb_isbn,stu_no,ord_quantity,tb_price,ord_date,im_no,is_draw'];
    }

    public function get_student_draw($userid)
    {
        $query = $this->db->query("select drawinfo.d_id,drawinfo.d_date,drawinfo.ord_id from students,drawinfo,orderinfo where drawinfo.Ord_id=orderinfo.Ord_id and orderinfo.Stu_No=students.stu_no and students.stu_no =$userid");

        return $query->result_array();
        //$ord = $row['ord_id,tb_isbn,stu_no,ord_quantity,tb_price,ord_date,im_no,is_draw'];

    }

    //1查看所有未被确定订书信息
    public function get_dingshudan()
    {
        $sql=
        "
        SELECT OrderInfo.Ord_id,OrderInfo.TB_isbn,OrderInfo.Ord_Quantity,OrderInfo.Stu_No,OrderInfo.Ord_date,OrderInfo.Im_No,OrderInfo.Is_Draw,TestBooksInfo.TB_Name
        FROM OrderInfo
        JOIN TestBooksInfo ON TestBooksInfo.TB_isbn = OrderInfo.TB_isbn
        WHERE OrderInfo.Im_No is NULL";
        //执行SQL
        $rs = $this->db->query($sql);
        //将查询结果放入到结果集中
        $result = $rs->result();
        //取查询结果的第一行

        return $this->db->query($sql);
    }
     
     //2[负责订单]根据管理员编号来获取该管理员已认领申请：
    public function ckdsd($user,$Ord_id=FALSE)
    {
        if ($Ord_id === FALSE)
        {
    //根据管理员编号来获取该管理员已认领申请：
        $query = $this->db->get_where('OrderInfo', array('Im_No' => $user));
        return $query->result_array();
        }
        //  根据 申请编号 获取单条已认领申请：
        $sql1 = "SELECT * FROM OrderInfo WHERE Im_No = ? AND Ord_id = ? AND Is_Draw = ? ";
        $query1=$this->db->query($sql1,[$user,$Ord_id,'0']);//要执行的 SQL
        $row = $query1->row_array();//转化成一个数组
        // $admin_id= $row['admin_id'];//提取数组里的元素
    }
    
     //3发送领书单+修改库存
    /**
     * 根据ord_id查询订书单详情
     * @param $Ord_id
     * @return mixed
     */
    public function ckdsdInfo($Ord_id) {
        $OrdInfo = $this->db->where(array('Ord_id' => $Ord_id))->limit(1)->get('OrderInfo')->row_array();

        $booksInfo = $this->db->where(array('TB_isbn' => $OrdInfo['TB_isbn']))->limit(1)->get('TestBooksInfo')->row_array();
        $data['OrdInfo'] = $OrdInfo;
        $data['booksInfo'] = $booksInfo;
        return $data;
    }
 
 
  /**
     * 修改
    * @param $Ord_id
    * @param $TB_isbn
    * @param $TB_Inventory
    * @return bool
    */
    public function modify_inventory($Ord_id, $TB_isbn, $TB_Inventory) {
        $inventory = $this->db->where('TB_isbn', $TB_isbn)->update('TestBooksInfo', array('TB_Inventory' => $TB_Inventory));
        $ord = $this->db->where('Ord_id', $Ord_id)->update('OrderInfo', array('Is_Draw' => 1));
        $drawInfo = $this->db->insert('DrawInfo', array('Ord_id' => $Ord_id, 'D_date' => date('Y-m-d')));
        if ($inventory && $ord && $drawInfo) {
        return true;
        } else {
        return false;
        }
    }
     //认领
     public function rldsd_model($Ord_id, $user) {
        $userid=$user['userid'];//$user是一个数组包括userID和usertype
        // echo $userid;
        // $Im_No_modify = $this->db->where('Ord_id', $Ord_id)->update('OrderInfo',array('Im_No'=>$userid));
        $Im_No_modify="UPDATE OrderInfo SET Im_No = ? WHERE Ord_id = ?";
        $this->db->query($Im_No_modify,[$userid,$Ord_id]);

        // $data = array('Im_No' => $userid);
        // $this->db->where('Ord_id', $Ord_id);
        // $this->db->update('OrderInfo',$data); 
        
        if ($Im_No_modify) {
            // echo "true11";
            return true;
            } else {
                // echo "true11";
            return false;
            }
    }


    public function get_current_page_records($limit, $start) 
	{
        $this->db->select('TestBooksInfo.TB_Name,TestBooksInfo.TB_isbn,TestBooksInfo.TB_Author,TestBooksInfo.TB_Price,CourseInfo.Cou_Name,Teachers.T_Name,MajorInfo.Maj_Name,CollegeInfo.Col_Name');
        $this->db->limit($limit, $start);
        $this->db->from('TestBooksInfo');
        $this->db->join('CourseTestBookInfo', 'TestBooksInfo.TB_isbn = CourseTestBookInfo.TB_isbn');
        $this->db->join('CourseInfo', 'CourseTestBookInfo.Cou_No = CourseInfo.Cou_No');
        $this->db->join('Teachers', 'CourseInfo.T_No = Teachers.T_No');
        $this->db->join('CourseClassInfo', 'CourseClassInfo.Cou_No = CourseInfo.Cou_No');
        $this->db->join('ClassInfo', 'CourseClassInfo.Cla_No = ClassInfo.Cla_No');
        $this->db->join('MajorInfo', 'MajorInfo.Maj_No = ClassInfo.Maj_No');
        $this->db->join('CollegeInfo', 'CollegeInfo.Col_No = MajorInfo.Col_No');
        
		$query = $this->db->get();
 //写到这里了！
		if ($query->num_rows() > 0) 
		{
			foreach ($query->result() as $row) 
			{
				$data[] = $row;
			}
			
			return $data;
		}
 
		return false;
    }

    public function get_current_page_records_search($limit, $start,$search_content,$search_type) 
	{
        $this->db->select('TestBooksInfo.TB_Name,TestBooksInfo.TB_isbn,TestBooksInfo.TB_Author,TestBooksInfo.TB_Price,CourseInfo.Cou_Name,Teachers.T_Name,MajorInfo.Maj_Name,CollegeInfo.Col_Name');
        $this->db->limit($limit, $start);
        $this->db->like($search_type, $search_content);
        $this->db->from('TestBooksInfo');
        $this->db->join('CourseTestBookInfo', 'TestBooksInfo.TB_isbn = CourseTestBookInfo.TB_isbn');
        $this->db->join('CourseInfo', 'CourseTestBookInfo.Cou_No = CourseInfo.Cou_No');
        $this->db->join('Teachers', 'CourseInfo.T_No = Teachers.T_No');
        $this->db->join('CourseClassInfo', 'CourseClassInfo.Cou_No = CourseInfo.Cou_No');
        $this->db->join('ClassInfo', 'CourseClassInfo.Cla_No = ClassInfo.Cla_No');
        $this->db->join('MajorInfo', 'MajorInfo.Maj_No = ClassInfo.Maj_No');
        $this->db->join('CollegeInfo', 'CollegeInfo.Col_No = MajorInfo.Col_No');
        
		$query = $this->db->get();
 //写到这里了！
		if ($query->num_rows() > 0) 
		{
			foreach ($query->result() as $row) 
			{
				$data[] = $row;
			}
			
			return $data;
		}
 
		return false;
    }


    
    public function get_shangpin_allNums()
    {
        $this->db->select('TestBooksInfo.TB_Name,TestBooksInfo.TB_isbn,TestBooksInfo.TB_Author,TestBooksInfo.TB_Price,CourseInfo.Cou_Name,Teachers.T_Name,MajorInfo.Maj_Name,CollegeInfo.Col_Name');
        $this->db->from('TestBooksInfo');
        $this->db->join('CourseTestBookInfo', 'TestBooksInfo.TB_isbn = CourseTestBookInfo.TB_isbn');
        $this->db->join('CourseInfo', 'CourseTestBookInfo.Cou_No = CourseInfo.Cou_No');
        $this->db->join('Teachers', 'CourseInfo.T_No = Teachers.T_No');
        $this->db->join('CourseClassInfo', 'CourseClassInfo.Cou_No = CourseInfo.Cou_No');
        $this->db->join('ClassInfo', 'CourseClassInfo.Cla_No = ClassInfo.Cla_No');
        $this->db->join('MajorInfo', 'MajorInfo.Maj_No = ClassInfo.Maj_No');
        $this->db->join('CollegeInfo', 'CollegeInfo.Col_No = MajorInfo.Col_No');
        return $this->db->count_all_results();
    }

    public function get_booksinfo($TB_isbn)
    {
        
        $this->db->select('TB_isbn,TB_Name,TB_Author,TB_Press,TB_Edition,TB_PublishDate,TB_Inventory,TB_Price');
        $this->db->where('TB_isbn', $TB_isbn);
        $this->db->from('TestBooksInfo');
        $query = $this->db->get();

        foreach ($query->result() as $row) 
			{
				$data[] = $row;
			}
			
			return $data;
        // $sql=
        // "
        // SELECT TB_isbn,TB_Name,TB_Author,TB_Press,TB_Edition,TB_PublishDate,TB_Inventory,TB_Price
        // FROM TestBooksInfo
        // WHERE TB_isbn = ?";
        // return $this->db->query($sql,$TB_isbn);
    }

    public function get_bookscourseinfo($TB_isbn)//根据教材isbn号获取很多很多相关信息
    {
        $this->db->select('TestBooksInfo.TB_Name,TestBooksInfo.TB_isbn,TestBooksInfo.TB_Author,TestBooksInfo.TB_Price,CourseInfo.Cou_Name,Teachers.T_Name,MajorInfo.Maj_Name,CollegeInfo.Col_Name');
        $this->db->where('TestBooksInfo.TB_isbn', $TB_isbn);
        $this->db->from('TestBooksInfo');
        $this->db->join('CourseTestBookInfo', 'TestBooksInfo.TB_isbn = CourseTestBookInfo.TB_isbn');
        $this->db->join('CourseInfo', 'CourseTestBookInfo.Cou_No = CourseInfo.Cou_No');
        $this->db->join('Teachers', 'CourseInfo.T_No = Teachers.T_No');
        $this->db->join('CourseClassInfo', 'CourseClassInfo.Cou_No = CourseInfo.Cou_No');
        $this->db->join('ClassInfo', 'CourseClassInfo.Cla_No = ClassInfo.Cla_No');
        $this->db->join('MajorInfo', 'MajorInfo.Maj_No = ClassInfo.Maj_No');
        $this->db->join('CollegeInfo', 'CollegeInfo.Col_No = MajorInfo.Col_No');
        $query = $this->db->get();

        foreach ($query->result() as $row) 
			{
				$data[] = $row;
			}
			
			return $data;
    }

    public function get_kuncunyemian()//获取库存信息相关的数据
    {
        $sql=
        "
        SELECT TB_isbn,TB_Name,TB_Author,TB_Press,TB_Edition,TB_PublishDate,TB_Inventory,TB_Price
        FROM TestBooksInfo
        ";
        return $this->db->query($sql)->result();
    }

	
    
	
}