function tabToggle(evt, idname) {
 var i, tabcontent, tablinks;tabcontent = document.getElementsByClassName("eztoc-tabcontent");
	for (i = 0; i < tabcontent.length; i++) { 
		tabcontent[i].style.display = "none"; 
	} 
	tablinks = document.getElementsByClassName("eztoc-tablinks"); 
	for (i = 0; i < tablinks.length; i++) { 
		tablinks[i].className = tablinks[i].className.replace(" active", "");
		 } 
	document.getElementById(idname).style.display = "block"; 

	evt.target.className += " active";
}

function eztocIsEmail(email) {
        var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
        return regex.test(email);
}
	

  //query form send starts here
jQuery(document).ready(function($) {

	var url = window.location.href; 
		if(url.indexOf('#technical-support') > -1){
		    $("#eztoc-technical").click();
		}else{	
			 $("#eztoc-default").click();
		}

    $(".eztoc-send-query").on("click", function(e){
            e.preventDefault();   
            var message     = $("#eztoc_query_message").val();  
            var email       = $("#eztoc_query_email").val();  
            var premium_cus = $("#saswp_query_premium_cus").val(); 
            
            if($.trim(message) !='' && $.trim(email) !='' && eztocIsEmail(email) == true){
             $.ajax({
                            type: "POST",    
                            url:ajaxurl,                    
                            dataType: "json",
                            data:{action:"eztoc_send_query_message",message:message,email:email, eztoc_security_nonce:eztoc_admin_data.eztoc_security_nonce},
                            success:function(response){                       
                              if(response['status'] =='t'){
                                $(".eztoc-query-success").show();
                                $(".eztoc-query-error").hide();
                              }else{                                  
                                $(".eztoc-query-success").hide();  
                                $(".eztoc-query-error").show();
                              }
                            },
                            error: function(response){                    
                            console.log(response);
                            }
                            });   
            }else{
                
                if($.trim(message) =='' && $.trim(email) ==''){
                    alert('Please enter the message, email and select customer type');
                }else{
                
                if($.trim(message) == ''){
                    alert('Please enter the message');
                }
                if($.trim(email) == ''){
                    alert('Please enter the email');
                }
                if(eztocIsEmail(email) == false){
                    alert('Please enter a valid email');
                }
                    
                }
                
            }                        

        });
    }); 