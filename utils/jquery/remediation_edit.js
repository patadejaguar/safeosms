$(document).ready(function(){
    
    var aLog = new Array();         // log string
    var aLogKey = new Object();     // array in JS has no string keys, use this to instead of it
    var aQuery = new Object();      // build query
    var evArray = new Object();		// evidence
    var estDateChanged = false;		// ensure estimated date is not changed without comment. request in email 2007-12-19.
    aQuery.comment_type = 'NONE';	// set default value of comment type
    evArray.length = 0;
    evArray.needComments = 0;
    
    var dw = $(this).width();
    var dh = $(this).height();
    // a cover div could give a full grey backgrougd over full page
    var cover_div = ({
        show : function(){
                $('<div id="full"></div>')
                .width(dw).height(dh)
                .css({backgroundColor:"#000000", marginTop:-1*dh, opacity:0, zIndex:10})
                .appendTo("body").fadeTo(1, 0.4);
            },
        hide : function(){
                $('#full').hide().remove();
            }
    });
    
    /*
    $(":image[src$='button_respond.png']").click(
    function(){
//        return true;
        var data = $(this).parents("form").serializeArray();
        var url = $(this).parents("form").attr('action');
        $(this).blur();        
        cover_div.show(); // show the grey cover div
        $('<div class="flora" title="Respond comments">Loading ....</div>')
        .load(url, data, function(){
            $(this).dialog({position:'center', width: 740, height: 280, resizable: true,
                buttons: {
                    'Continue': function() {  // on button "continue" clicked
            		    aQuery.comment_topic = $('input[name="comment_topic"]').val();
            		    aQuery.comment_body = $('textarea[name="comment_body"]').val();
            		    aQuery.comment_log = "This is a response.";
            		    aQuery.comment_parent = $('input[name="comment_parent"]').val();
            		    aQuery.poam_id = $('input[name="poam_id"]').val();

            		    $.post('remediation_save.php', aQuery, function(r,t,x){
                            eval(r); // to redirect bowser by JS
                        });
                    },
            		'Cancel': function() {    // on button "cancel" clicked
            		    cover_div.hide();
            			$(this).dialogClose();
            			$('.ui-dialog').remove();
            		}
                }
            });
            $('textarea[name="comment_body"]').focus();
        });
        return false;
    });
    */
    
    $(":input[@title='Submit Evidence']").click(
    function(){
//        return true;
        var data = $(this).parents("form").serializeArray();
        var url = $(this).parents("form").attr('action');
        $(this).blur();        
        cover_div.show(); // show the grey cover div
        $('<div class="flora" title="Upload Evidence">Loading ....</div>')
        .load(url, data, function(){
            $(this).dialog({position:'center', width: 540, height: 250, resizable: true,
                buttons: {
                    'Continue': function() {  // on button "continue" clicked
            		    $('#upload_ev').submit();
                    },
            		'Cancel': function() {    // on button "cancel" clicked
            		    cover_div.hide();
            			$(this).dialogClose();
            			$('.ui-dialog').remove();
            		}
                }
            });
//            $('textarea[name="comment_body"]').focus();
        });
        return false;
    });
    
    $(":input[@title='Save or Submit']").click(
    function(){
        //return true;
        if (aLog.length < 1){
            alert('You have no changes to submit.');
            return false;
        }
        for (prop in aQuery)
        {
            if ((prop!='comment_type') && (aQuery[prop]=='' || aQuery[prop]=='NONE')){
                var field = prop.substr(5).replace('_',' ');
                alert('You cannot change '+field+' to blank.');
                return false;
            }
        }
        // comments not needed
        /**
            Comments only needed when :
                *1. SSO denied. -- deleted on 20080130  | ('DENIED'!=aQuery.poam_action_status) && 
                2. EST date changed.
                3. Evidence denied.
            If evidence provided, a popup window will also appered.
        **/
        if (!estDateChanged && (evArray.needComments<=0)){
        	aQuery.poam_id = $('input[name="remediation_id"]').val();
            if (evArray.length > 0){
    		    for (var ev in evArray){
    		        evArray[ev].remediation_id = aQuery.poam_id;
    		        if(ev!='length' && ev!='needComments'){
        		        $.post('evidence_save.php', evArray[ev], function(r,t,x){
        		            eval(r);
        		        });
    		        }
    		    }
            }
            else{
    		    $.post('remediation_save.php', aQuery, function(r,t,x){
                    eval(r); // to redirect bowser by JS
                });
            }
        	return false;
        }

        var data = $(this).parents("form").serializeArray();
        var url = $(this).parents("form").attr('action');

        $(this).blur();        
        cover_div.show(); // show the grey cover div
        
        $('<div class="flora" title="Comment your changes">Loading ....</div>')
        .load(url, data, function(){
            $(this).dialog({position:'center', width: 800, height: 450, resizable: true,
                buttons: {
            		'Continue': function() {  // on button "continue" clicked
            		    if($('input[name="comment_topic"]').val().length < 1){
            		        alert('Comment topic seems too short.');
            		        $('input[name="comment_topic"]').focus();
            		        return;
            		    }
            		    if($('textarea[name="comment_body"]').val().length < 1){
            		        alert('Comment body seems too short.');
            		        $('textarea[name="comment_body"]').focus();
            		        return;
            		    }
            		    // maybe we can more simple ...
            		    aQuery.comment_topic = $('input[name="comment_topic"]').val();
            		    aQuery.comment_body = $('textarea[name="comment_body"]').val();
            		    aQuery.comment_log = $('textarea[name="comment_log"]').val();
            		    aQuery.comment_parent = $('input[name="comment_parent"]').val();
            		    aQuery.poam_id = $('input[name="poam_id"]').val();
            		    
            		    if(aQuery.poam_action_status) aQuery.comment_type='SSO';
            		    if(aQuery.poam_action_date_est) aQuery.comment_type='EST';
            		    
            		    if (evArray.length > 0){
            		        var return_js = '';
                		    for (var ev in evArray){
                		        if(ev!='length' && ev!='needComments'){
                    		        evArray[ev].remediation_id = aQuery.poam_id;
                    		        evArray[ev].comment_topic = aQuery.comment_topic;
                    		        evArray[ev].comment_body = aQuery.comment_body;
                    		        evArray[ev].comment_log = aQuery.comment_log;
                    		        evArray[ev].comment_parent = aQuery.comment_parent;
                    		        evArray[ev].comment_type = aQuery.comment_type;
                    		        $.post('evidence_save.php', evArray[ev], function(r,t,x){
                    		            eval(r);
                    		        });
                		        }
                		    }
            		    }
            		    else{
                		    $.post('remediation_save.php', aQuery, function(r,t,x){
                                eval(r); // to redirect browser by JS
                            });
            		    }
            		},
            		'Cancel': function() {    // on button "cancel" clicked
            		    cover_div.hide();
            			$(this).dialogClose();
            			$('.ui-dialog').remove();
            		}
    	       }});
            $('textarea[name="comment_log"]').val(aLog.join("\n")); // fill log field
        });
        return false;
    });

    
    $(":image[src$='button_modify.png']").click(function(){
        var box = $(this).next('span');
        var old_value = box.html();
        
        var data = $(this).parents("form").serializeArray();
        var url = $(this).parents("form").attr('action');
        var action = $(this).prevAll('b').text();
        
        box.html('Loading ....').load(url, data, function(){
            
            var input_JQ_obj = $(this).children(':first'); // jQuery object
            var input_obj = input_JQ_obj.get(0); // DOM object
            var new_value = null;
            var hasDatePicker = (input_JQ_obj.attr('className') == 'date_picker') ? true : false;
            var reg_ymd = /\d{4}-\d{2}-\d{2}/;
            
            if (hasDatePicker){
                input_JQ_obj.datepicker({ 
                  onSelect: function(dateText) { 
                        $.datepicker.disableFor(input_JQ_obj);
                        input_JQ_obj.attr("disabled","").focus();
                  }
                  ,speed:''
                  ,dateFormat:'YMD-'
                });
            }
            
            input_JQ_obj.css("border","1px dotted red").focus().blur(function(){
                // if the input has a date picker, do sth specially.
                if ($('#datepicker_div:visible').size() > 0){
                    return;
                }
                
                // new_value is the new display value
                if (input_obj.nodeName == 'SELECT'){
                    new_value = input_obj.options[input_obj.selectedIndex].label;
                    // if sso approve or deny action, change save image to submit image
                    if (input_obj.name == 'poam_action_status'){
                        $(":input[@title='Save or Submit']").val('Submit');
                    }
                }
                else{
                    new_value = input_JQ_obj.val();
                }
                
                // if user provide a wrong format date, we will force it back.
                if (hasDatePicker && !reg_ymd.exec(new_value)) {
                    alert('You must choose or provide a date with style yyyy-mm-dd here.');
                    new_value = old_value;
                }
                
                if ($.trim(new_value) != $.trim(old_value)){
                    box.html(new_value).css('color','red').prev('input').show(); // hight light if modified
                    // bulid query and log string
                    if (input_obj.className == 'ev'){
                        eval('evArray.ev_'+input_obj.id+" = {'action':input_obj.name, 'new_value':input_JQ_obj.val(), 'ev_id':input_obj.id};");
                        evArray.length++;
                        if (input_JQ_obj.val() == 'DENIED'){
                            evArray.needComments++;
                        }
                        else{
                            evArray.needComments--;
                        }
                        // need special log
                    }
                    else{
                        eval('aQuery.'+input_obj.name+'=input_JQ_obj.val();');
                    }
                    eval('var isset = aLogKey.'+input_obj.name+';'); 
                    if (isset > 0){ // if modify more than once, we should replace it.
                        aLog[isset-1] += " => "+new_value;      // not perfect ..
                    }
                    else{
                        aLog.push(action+' '+old_value+" => "+new_value);
                        eval('aLogKey.'+input_obj.name+'=aLog.length;');
                        if ((input_obj.name == 'poam_action_date_est') && ($.trim(old_value) != '') && ($.trim(old_value) != '0000-00-00')) estDateChanged = true;
                    }
                }
                else{
                    box.html(old_value).prev('input').show(); // not modified, old view
                }
            });
        });
        $(this).hide();
        return false;
    });
/*    
    $(':image').click(function(){
        if ($(this).val() == 'New Comment'){
            alert('Under construction. :)');      
            return false;
        }
    });
*/    
    $(":input[@value='Go Back']").click(function(){
        if (aLog.length > 0)
        return confirm("You have some changes in current page, do you really want to dismiss these work? \n Press 'Yes' to leave or 'No' to stay.");
    });
    
    $(":input[@title='Submit Evidence Change']").click(function(){
        $(":input[@title='Save or Submit']").click();
    });
    
    $("img.expend_btn").css({'cursor':'pointer'}).click(function(){
        src = $(this).attr('src');
        $(this).attr('src', (src=='images/contract.gif')?'images/expand.gif':'images/contract.gif')
        .parents('table.tbline').nextAll('table.tipframe:first').toggle();
    });
    
//    $("img.expend_btn + b:contains('Finding Detail')").prev().click();
//    $("img.expend_btn + b:contains('Vulnerability Detail')").prev().click();
    $("img.expend_btn + b:contains('Finding Audit Log')").prev().click();
});
