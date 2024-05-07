<?php
class UserModel
{
    
    // private $conn;
    // public function __construct($conn)
    // {
    //     $this->conn = $conn;
    // }
    public function getAllUsers()
    {
        $db = file_get_contents('db/db.json'); 
        $json_data = json_decode($db,true); 
        // $query = "SELECT * FROM user";
        // $result = mysqli_query($this->conn, $query);
        // $Users = [];
        // while ($row = mysqli_fetch_assoc($result)) {
        //     $Users[] = $row;
        // }
        http_response_code(200);
        return $json_data;
    }
    public function getUserById($id)
    {
        // $query = "SELECT * FROM user WHERE id = $id";
        // $result = mysqli_query($this->conn, $query);
        // $User = mysqli_fetch_assoc($result);
        http_response_code(200);
        return ["status" => 'code Working', "User" => $id];
    }
    public function addUser($data)
    {
        // $user_name = $data['user_name'];
        // $user_code = $data['user_code'];
        // $user_email = $data['user_email'];
        // $user_phone = $data['user_phone'];
        // $user_address = $data['user_address'];
        // $user_designation = $data['user_designation'];
        // $user_joining_date = $data['user_joining_date'];

        // $query = "INSERT INTO user (user_name, user_code, user_email, user_phone, user_address, user_designation, user_joining_date) VALUES ( ?, ?, ?, ?, ?, ?, ?)";
        // $result = $this->conn->prepare($query);
        // $result->bind_param('sssssss', $user_name, $user_code, $user_email, $user_phone, $user_address, $user_designation, $user_joining_date);
        // if ($result->execute()) {
        //     return true;
        // } else {
        //     return false;
        // }
        http_response_code(202);
        return ["status" => 'success', "User" => $data];
    }
    public function updateUser($id, $data)
    {
        // $user_name = $data['user_name'];
        // $user_code = $data['user_code'];
        // $user_email = $data['user_email'];
        // $user_phone = $data['user_phone'];
        // $user_address = $data['user_address'];
        // $user_designation = $data['user_designation'];
        // $user_joining_date = $data['user_joining_date'];
        // $query = "UPDATE user SET user_name = '$user_name', user_code = '$user_code', user_email = '$user_email', user_phone = '$user_phone', user_address = '$user_address', user_designation = '$user_designation', user_joining_date = '$user_joining_date' WHERE id = $id";
        // $result = mysqli_query($this->conn, $query);
        // if ($result) {
        //     return true;
        // } else {
        //     return false;
        // }
    }
    public function deleteUser($id)
    {
        // $query = "DELETE FROM user WHERE id = $id";
        // $result = mysqli_query($this->conn, $query);
        // if ($result) {
        //     return true;
        // } else {
        //     return false;
        // }
    }
}
