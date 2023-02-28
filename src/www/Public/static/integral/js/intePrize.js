	var loading = "";
	loading += "<div style='text-align: center; padding-top: 150px;'>";
	loading += "<img src='/Public/static/integral/images/loading.gif' style='width: 60px; height: 60px;'>";
	loading += "</div>";
	
	$(document).ready(function(){
            var postCount = 0;
             //显示隐藏产品div
            $(".addActivity").click(function(){
                postCount++;
                if(postCount % 2 != 0){
                    var activityInfo = document.getElementById("activityInfo").value;
                    var pt = document.getElementById("pt").value;
                    $.getJSON("{:U('findSlActivityInfo')}",{"activityInfo":activityInfo,"type":pt},function(json){
                        alert(json.status);
                        initPagination(json);
                    });
                    $(".activityInfo_div").empty().append(loading);
                }
               $(".activity_div").slideToggle();
            });

            var userCount = 0;
             //显示隐藏产品div
            $(".addUser").click(function(){
                var pt = document.getElementById("pt").value;
                var activityId = document.getElementById("pId").value
                var userInfo = document.getElementById("userInfo").value;
                if(activityId != null && activityId != ""){
                    userCount++;
                    if(userCount % 2 != 0){
                        $.getJSON("{:U('findSlUserInfo')}",{"userInfo":userInfo,"activityId":activityId,"type":pt},function(json){
                                showUserInfo(json);
                            });
                        $(".userInfo_div").empty().append(loading);
                    }
                   $(".user_div").slideToggle();
                }else{
                    alert("请先选择获奖地址!");
                }
            });

            var ptCount = 0;
            $(".pt").change(function(){
                document.getElementById("activityInfo").value = "";
                document.getElementById("pId").value = "";
                document.getElementById("pTitle").value = "";
                ptCount++;
                var pt = document.getElementById("pt").value;
                var activityInfo = "";
                $.getJSON("{:U('findSlActivityInfo')}",{"activityInfo":activityInfo,"type":pt},function(json){
                    initPagination(json);
                });
                $(".activityInfo_div").empty().append(loading);
               if(ptCount == 0){
                       $(".activity_div").slideToggle();
               }
            });
	 });
	
	function initPagination(json){
            var str =""; 
            var data = json.list;
            if(data.length != 0){
            str+= "<table class='gridView' style='BORDER-COLLAPSE: collapse' border=1>";
            str+="<tbody>";
            str+="<tr style='background-color: #F2F2F2;font-size: 18px; height: 55px;'><th style='text-align: center;'>序号</th>";
            str+="<th style='text-align: center;'>名称</th></tr>";
            for(var i = 0; i < data.length; i++){
                str += "<tr style='font-size: 14px;color: #000000; height: 48px;'><td style='text-align: center;'>" + data[i].id + "</td>";
                str += "<td style='width:240px;text-align: center;'><a onclick='checkActivityInfo(this)' id='"+data[i].id+"' name='"+data[i].title+"' class='postInfo' style='padding-left: 10px; color: blue;'>" + data[i].title + "</a></td></tr>";
            }
                str += "</tbody></table><br>";

                str += "【<label id='currentPage' >"+data[0].cp+"</label>/<label id='totalPages' >"+data[0].tp+"</label>】";

                str += "<a style='padding-left: 100px;' onclick='previouPage()'>上一页</a><a style='margin-left: 30px;' onclick='nextPage()'>下一页</a>";
            }else{
                str += "<label style='margin-left:70px;margin-top:300px;'>没有找到相关信息！</label>";
            }
            $(".activityInfo_div").empty().append(str);
	 }
	 
	 function checkActivityInfo(t){
            var cName = t.name;
            var cId = t.id;
            //var p = cInfo.split("_");
	    document.getElementById("pId").value = cId; 
            document.getElementById("pTitle").value = cName; 

            //更改发送用户信息
            if(document.getElementById("toUsers").value != "everyOne"){
                document.getElementById("toUsers").value = "";
            }
            document.getElementById("userInfo").value = "";
            var activityId = document.getElementById("pId").value
            var pt = document.getElementById("pt").value;
            var userInfo = "";
            $.getJSON("{:U('findSlUserInfo')}",{"userInfo":userInfo,"activityId":activityId,"type":pt},function(json){
                showUserInfo(json);
            });
            //$(".userInfo_div").empty().append(loading);
	}
	
	//上一页
	function previouPage(){
            //$(".post_div").empty().append(loading);
            var cp = document.getElementById("currentPage").innerHTML;

            if(cp == "1"){
                    alert("已经是首页!");
            }else{
                $(".activityInfo_div").empty().append(loading);
                var activityInfo = document.getElementById("activityInfo").value;
                var pt = document.getElementById("pt").value;
                $.getJSON("{:U('findSlActivityInfo')}",{"activityInfo":activityInfo,"type":pt,"currentPage":cp,"pageMethod":"previous"},function(json){
                    initPagination(json);
                });
            }
	}
	
	//下一页
	function nextPage(){
            //$(".post_div").empty().append(loading);
            var cp = document.getElementById("currentPage").innerHTML;
            var tp = document.getElementById("totalPages").innerHTML;
            if(cp == tp){
                    alert("已经是最后一页!");
            }else{
                $(".activityInfo_div").empty().append(loading);
                var activityInfo = document.getElementById("activityInfo").value;
                var pt = document.getElementById("pt").value;
                $.getJSON("{:U('findSlActivityInfo')}",{"activityInfo":activityInfo,"type":pt,"currentPage":cp,"pageMethod":"next"},function(json){
                        initPagination(json);
                });
            }
	}
	
	function findActivityInfo(){
            var activityInfo = document.getElementById("activityInfo").value;
            var pt = document.getElementById("pt").value;
            $(".activityInfo_div").empty().append(loading);
            $.getJSON("{:U('findSlActivityInfo')}",{"activityInfo":activityInfo,"type":pt},function(json){
                initPagination(json);
            });
	}
	
	
	
	//选择用户
	function showUserInfo(data){
            var str =""; 
            if(data.length != 0){
                str+= "<table class='gridView' style='BORDER-COLLAPSE: collapse' border=1>";
                str+="<tbody>";
                str+="<tr style='background-color: #F2F2F2;font-size: 18px; height: 55px;'><th style='text-align: center;width: 55px;'>序号</th>";
                str+="<th style='text-align: center; width: 120px;'>用户名</th><th style='text-align: center; width: 120px;'>昵称</th></tr>";
                for(var i = 0; i < data.length; i++){
                        //alert(data[i].id + "-" + data[i].title);

                    str += "<tr style='font-size: 14px;color: #000000; height: 48px;'><td>" + data[i].id + "</td>";
                    str += "<td>" + data[i].mobile + "</td>";
                    str += "<td><a onclick='checkUserInfo(this)' id='"+data[i].id+"' name='"+data[i].nickName+"' class='userInfo' style='padding-left: 10px; color: blue;'>" + data[i].nickName + "</a></td></tr>";
                }
                str += "</tbody></table><br>";

                str += "【<label id='ucurrentPage' >"+data[0].cp+"</label>/<label id='utotalPages' >"+data[0].tp+"</label>】";

                str += "<a style='padding-left: 60px;' onclick='upreviouPage()'>上一页</a><a style='margin-left: 30px;' onclick='unextPage()'>下一页</a>";
            }else{
                str += "<label style='margin-left:70px;margin-top:300px;'>没有找到相关信息！</label>";
            }
            $(".userInfo_div").empty().append(str);
	}
	
	//上一页
	function upreviouPage(){
            //$(".post_div").empty().append(loading);
            var cp = document.getElementById("ucurrentPage").innerHTML;
            var pt = document.getElementById("pt").value;
            var activityId = document.getElementById("pId").value
            var userInfo = document.getElementById("userInfo").value;
            if(cp == "1"){
                alert("已经是首页!");
            }else{
                $(".userInfo_div").empty().append(loading);
                $.getJSON("{:U('findSlUserInfo')}",{"userInfo":userInfo,"activityId":activityId,"type":pt,"currentPage":cp,"pageMethod":"previous"},function(json){
                    showUserInfo(json);
                });
            }
	}

	//下一页
	function unextPage(){
            //$(".post_div").empty().append(loading);
            var cp = document.getElementById("ucurrentPage").innerHTML;
            var tp = document.getElementById("utotalPages").innerHTML;
            var pt = document.getElementById("pt").value;
            var activityId = document.getElementById("pId").value
            var userInfo = document.getElementById("userInfo").value;
            if(cp == tp){
                alert("已经是最后一页!");
            }else{
                $(".userInfo_div").empty().append(loading);
                $.getJSON("{:U('findSlUserInfo')}",{"userInfo":userInfo,"activityId":activityId,"type":pt,"currentPage":cp,"pageMethod":"next"},function(json){
                    showUserInfo(json);
                });
            }
	}

	function checkUserInfo(t){
            var checkedUser = document.getElementById("toUsers").value;
            //alert(checkedUser);
            var cName = t.name;
            var cId = t.id;
            //alert(checkedUser.indexOf(p[0]));
            //alert(p[1]);
            if(checkedUser.indexOf(cId) >= 0){
                alert("该用户已选择!");
            }else{
                document.getElementById("toUsers").value += cId + ","; 
                //document.getElementById("userName").value += cId + ","; 
            }
	}


	function findUserInfo(){
            var pt = document.getElementById("pt").value;
            var activityId = document.getElementById("pId").value
            var userInfo = document.getElementById("userInfo").value;
            $(".userInfo_div").empty().append(loading);
            $.getJSON("{:U('findSlUserInfo')}",{"userInfo":userInfo,"activityId":activityId,"type":pt},function(json){
                showUserInfo(json);
            });
	}