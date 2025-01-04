<?php
// 处理表单提交
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = htmlspecialchars($_POST["name"]);
    $from = htmlspecialchars($_POST["from"]);
    $expectation = htmlspecialchars($_POST["expectation"]);
    $target_dir = "uploads/";
    $image_path = '';
    $errorMessage = '';
    $submission_time = date('Y-m-d H:i:s');

    if (!empty($_FILES["fileToUpload"]["name"])) {
        $target_file = $target_dir. basename($_FILES["fileToUpload"]["name"]);
        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // 检查文件是否为图片
        $check = @getimagesize($_FILES["fileToUpload"]["tmp_name"]);
        if ($check === false) {
            $errorMessage = "文件不是图片。";
            $uploadOk = 0;
        }

        // 检查文件格式
        if ($imageFileType!= "jpg" && $imageFileType!= "png") {
            $errorMessage = "仅支持 JPG 和 PNG 格式的文件。";
            $uploadOk = 0;
        }

        // 检查文件是否已存在
        if (file_exists($target_file)) {
            $errorMessage = "文件已存在。";
            $uploadOk = 0;
        }

        // 检查文件大小
        if ($_FILES["fileToUpload"]["size"] > 5000000) {
            $errorMessage = "文件太大。";
            $uploadOk = 0;
        }

        if ($uploadOk == 1) {
            if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
                $image_path = $target_file;
            } else {
                $errorMessage = "上传文件失败。";
            }
        }
    }

    if (empty($errorMessage)) {
        // 将用户信息写入文件
        $data = [
            "name" => $name,
            "from" => $from,
            "expectation" => $expectation,
            "image_path" => $image_path,
            "submission_time" => $submission_time
        ];
        $data_file = "data/". uniqid(). ".json";
        file_put_contents($data_file, json_encode($data));
        $successMessage = "数据保存成功。";
        // 重定向到当前页面（你也可以重定向到其他展示页面）
        header("Location: ". htmlspecialchars($_SERVER["PHP_SELF"]));
        exit;
    }
}

// 读取所有数据
$files = glob("data/*.json");
$wishes = [];
if (!empty($files)) {
    foreach ($files as $file) {
        $content = file_get_contents($file);
        $wishes[] = json_decode($content, true);
    }
}
?>

<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>2025 新年快乐</title>
    <style>
        body {
            background-image: url('xxx.jpg');
            background-size: cover;
            background-repeat: no-repeat;
            font-family: Arial, sans-serif;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }
     .container {
            background-color: rgba(255, 255, 255, 0.8);
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
            width: 80%;
            max-width: 800px;
        }
        h1 {
            text-align: center;
            color: #333;
        }
     .form-group {
            margin-bottom: 15px;
        }
     .form-group label {
            display: block;
            margin-bottom: 5px;
            color: #555;
        }
     .form-group input,
     .form-group textarea {
            width: 100%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
     .form-group input[type="file"] {
            margin-top: 5px;
        }
     .form-group button {
            padding: 10px 20px;
            background-color: #007BFF;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
     .form-group button:hover {
            background-color: #0056b3;
        }
     .wishes {
            margin-top: 30px;
        }
     .wish {
            border: 1px solid #ccc;
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 15px;
            background-color: white;
        }
     .wish h3 {
            margin-top: 0;
            color: #333;
        }
     .wish p {
            color: #555;
        }
     .wish img {
            max-width: 100%;
            height: auto;
            display: block;
            margin-top: 10px;
            if (empty($image_path)) {
                display: none;
            }
        }
        /* 新增样式用于显示时间标签 */
     .time-label {
            font-size: 12px;
            color: #999;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <script>
        <?php
        if (!empty($errorMessage)) {
            echo "alert('$errorMessage');";
        }
        if (!empty($successMessage)) {
            echo "alert('$successMessage');";
        }
       ?>
    </script>
    <div class="container">
        <h1>2025 新年快乐</h1>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label for="name">姓名</label>
                <input type="text" id="name" name="name" required>
            </div>
            <div class="form-group">
                <label for="from">来自哪里</label>
                <input type="text" id="from" name="from" required>
            </div>
            <div class="form-group">
                <label for="expectation">对2025年的期望</label>
                <textarea id="expectation" name="expectation" required></textarea>
            </div>
            <div class="form-group">
                <label for="fileToUpload">上传图片（可选）</label>
                <input type="file" id="fileToUpload" name="fileToUpload">
            </div>
            <div class="form-group">
                <button type="submit">提交</button>
            </div>
        </form>
        <div class="wishes">
            <h2>大家的期望</h2>
            <?php if (!empty($wishes)):?>
                <?php foreach ($wishes as $wish):?>
                    <div class="wish">
                        <h3><?php echo $wish["name"];?> 来自 <?php echo $wish["from"];?></h3>
                        <p>期望: <?php echo $wish["expectation"];?></p>
                        <?php if (!empty($wish["image_path"])):?>
                            <img src="<?php echo $wish["image_path"];?>" alt="用户上传图片">
                        <?php endif;?>
                        <span class="time-label">提交时间: <?php echo $wish["submission_time"];?></span>
                    </div>
                <?php endforeach;?>
            <?php else:?>
                <p>暂无数据。</p>
            <?php endif;?>
        </div>
    </div>
</body>
</html>
