<?php
$tmpPath = $_SERVER['TEMP'] . "\\" . uniqid() . '.tmp';
$subject = str_replace("\n", "", $_POST['base64_file']);
$pattern = '/^data\:image\/(jpg|jpeg|png|gif);base64,(.*)/';
preg_match($pattern, $subject, $matches);
$base64_data = base64_decode($matches[2]);
file_put_contents($tmpPath, $base64_data);
$_FILES['file'] = [
    'name' => $_POST['base64_file_name'],
    'type' => 'image' . '/' . $matches[1],
    'tmp_name' => $tmpPath,
    'error' => 0,
    'size' => filesize($tmpPath)
];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>

<body>
    <img src="" id="yulan">
    <input type="file" id="file" onchange="showPhoto()">
    <script>
        function showPhoto() {
            //文件对象 
            var file = document.getElementById("file").files[0];
            if (!file) return;

            //读取后辍,判断是否允许的文件
            var fileSuffix = file.name.substring(file.name.length - 4);
            var allowFile = ".jpg|.jpeg|.png|.gif";
            if (allowFile.indexOf(fileSuffix.toLowerCase()) == -1) {
                alert("请使用" + allowFile + "后辍的文件");
                return false;
            }

            console.log(file)

            var beforeImg = document.createElement('img');
            beforeImg.src = file.name
            beforeImg.style = "width:300px;";
            document.body.appendChild(beforeImg);


            var reader = new FileReader()

            reader.readAsDataURL(file);
            reader.onload = function (e) {
                console.log(e)
                var imgBase64Data = e.target.result;
                // if (e.total < 1048576) {
                //     return imgBase64Data;
                // }

                //对图片进行缩小处理,然后再上传
                compressPhoto(imgBase64Data, function (imgBase64DataBack) {
                    var afterImg = document.createElement('img');
                    afterImg.src = imgBase64DataBack
                    afterImg.style = "width:300px;";
                    document.body.appendChild(afterImg);
                });

            }

        }

        /**
        * js利用canvas对图像进行压缩处理
        * @param {string}    imgBase64Data     图像base64数据
        * @param {string}    maxWidth          最大高度
        * @param {function}  maxHeight         最大宽度
        * @param {boolean}   fun               回调函数，参数为处理后的图像数据
          使用示例：
          compressPhoto(imgBase64Data,maxWidth,maxHeight,function(imgBase64Data){
              //返回图片数据后的处理
          })
        */
        function compressPhoto(imgBase64Data, fun) {
            var img = new Image();

            // 缩放图片需要的canvas
            var canvas = document.createElement('canvas');
            var context = canvas.getContext('2d');

            // base64地址图片加载完毕后
            img.onload = function () {
                // 图片原始尺寸
                var originWidth = this.width;
                var originHeight = this.height;
                // 目标尺寸
                var targetWidth = originWidth,
                    targetHeight = originHeight;
                // var targetWidth = originWidth*3/4, targetHeight = originHeight*3/4;

                // canvas对图片进行缩放
                canvas.width = targetWidth;
                canvas.height = targetHeight;
                // 清除画布
                context.clearRect(0, 0, targetWidth, targetHeight);
                // 图片压缩
                context.drawImage(img, 0, 0, targetWidth, targetHeight);

                var base = canvas.toDataURL("image/jpeg", 0.9); //canvas转码为base64               
                fun(base); //返回处理
            };

            img.src = imgBase64Data;
        }
    </script>
</body>

</html>