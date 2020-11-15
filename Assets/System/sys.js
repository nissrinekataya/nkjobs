

class SYS{
    static login(that){
        let email = $(that).parents("form").find("input[name='email']").val();
        let password = $(that).parents("form").find("input[name='password']").val();
        let fd = new FormData();
        fd.append("key","login/submitlogin");
        fd.append("email",email);
        fd.append("password",password);
        this.xhr(fd, $(that).attr("to") );
    }
    static xhr(_data,OutContainer,_success){
        let _url = SELF_DIR;
        let cfg = {
            url: _url,
            data: _data,
            method:"POST",
            cache: false,
            contentType: false,
            processData: false,
        };
        cfg.xhr = function(){
            var xhr = new window.XMLHttpRequest();
            //Upload progress
            xhr.upload.addEventListener("progress", function(evt){
                if (evt.lengthComputable) {
                    var percentComplete = evt.loaded / evt.total;
                    //Do something with upload progress
                    //console.log(percentComplete);
                    percentComplete = percentComplete * 100;
                    percentComplete = parseInt(percentComplete);
                    if(OutContainer != null){
                        document.getElementById(OutContainer).innerHTML = "Uploading ..." + percentComplete + " % " ;
                    }
                }
            }, false);
            //Download progress
            xhr.addEventListener("progress", function(evt){
            if (evt.lengthComputable) {
                var percentComplete = evt.loaded / evt.total;
                //Do something with download progress
                //console.log(percentComplete);
                percentComplete = percentComplete * 100;
                percentComplete = parseInt(percentComplete);
                if(OutContainer != null){
                    document.getElementById(OutContainer).innerHTML = "Downloading ..." + percentComplete + " % " ;
                }
            }
            }, false);
            return xhr;
        };
        cfg.success = function (resp) {
            try{
                if (_success) _success(resp,OutContainer);
                else $(`#${OutContainer}`).html(resp);
            }
            catch(error){
                console.log(error);
                if(OutContainer != null){
                    $("#"+OutContainer).html(error);
                }
            }
        }
        $.ajax(cfg);
    }
    static XHRForm(that){
        let fd = new FormData($(that).parents('form')[0]);
        if($(that).parents('form').find("[name='html']").length > 0){
            if(CKEDITOR.instances.html){
                fd.append("html",CKEDITOR.instances.html.getData());
            }
        }
        SYS.xhr_post(null,fd,"text",$(that).attr('to'),function(r,o){
            $(`#${o}`).html(r);
        });
    }
    static XHRFct(fct,to){
        let fd = new FormData();
        fd.append("fct",fct);
        SYS.xhr_post(null,fd,"text",to,function(r,o){
            $(`#${o}`).html(r);
        });
    }
    static LoadXHR(out,fct){
        let fd = new FormData();
        fd.append("key",fct);
        SYS.xhr_post(null,fd,"text",out,function(r,o){
            $(`#${o}`).html(r);
        });
    }
    static xhr_post(_url,_data,_type,OutContainer,_success,_error =null){
        if(_url == null){
            _url = BASE_DIR ;
        }
        this.OutContainer = OutContainer;
        if(OutContainer == null) OutContainer="CT1";
        if(OutContainer == "no") OutContainer=null;
        if(OutContainer != null){
            var t = document.getElementById(OutContainer);
            if(document.getElementById(OutContainer) == null){
                t = document.createElement('div');
                t.id = OutContainer;
                document.body.appendChild(t);
            }
            document.getElementById(OutContainer).innerHTML = "Loading ...";
            document.getElementById(OutContainer).style.display = "block";
        }
        //console.log(`to url ${_url}`);
        //console.log(`method ${_type}`);
        let cfg = {
            url: _url,
            type: _type,
            data: _data,
            method:"POST",
            cache: false,
            contentType: false,
            processData: false,
        };
        //to track the progress of upload
        cfg.xhr = function(){
            var xhr = new window.XMLHttpRequest();
            //Upload progress
            xhr.upload.addEventListener("progress", function(evt){
                if (evt.lengthComputable) {
                    var percentComplete = evt.loaded / evt.total;
                    //Do something with upload progress
                    //console.log(percentComplete);
                    percentComplete = percentComplete * 100;
                    percentComplete = parseInt(percentComplete);
                    if(OutContainer != null){
                        document.getElementById(OutContainer).innerHTML = "Uploading ..." + percentComplete + " % " ;
                    }
                }
            }, false);
            //Download progress
            xhr.addEventListener("progress", function(evt){
            if (evt.lengthComputable) {
                var percentComplete = evt.loaded / evt.total;
                //Do something with download progress
                //console.log(percentComplete);
                percentComplete = percentComplete * 100;
                percentComplete = parseInt(percentComplete);
                if(OutContainer != null){
                    document.getElementById(OutContainer).innerHTML = "Downloading ..." + percentComplete + " % " ;
                }
            }
            }, false);
            return xhr;
        };

        

        // handle error from xhr
        cfg.error= function (jqXHR, textStatus) {
            console.log({ "Error- Status: ": textStatus," jqXHR Status: ": jqXHR.status, " jqXHR Response Text:": jqXHR.responseText });
            $("#"+OutContainer).html(jqXHR.responseText);
            
            if(_error){
                _error(jqXHR, textStatus);
            }
            //redo request if server responded with error
            if(jqXHR.status == 500 ){
                //SYS.xhr_post(_url,_data,_type,OutContainer,_success,_error);
            }
            else if(jqXHR.status == 419){
                alert("Session ended, need to refresh page");
                location = location;
            }
        };
        //success of ajax call
        cfg.success = function (resp) {
            try{

                _success(resp,OutContainer);
            }
            catch(error){
                console.log(error);
                if(OutContainer != null){
                    $("#"+OutContainer).html(error);
                }
            }
        }
        $.ajax(cfg);
        
    }
    static handleDALFileUpload(that){
        let input = $(that).parents(".form-group").find("input[type='file']")[0];
        if(input.files.length == 0){
            SYS.dialog("No file provided","Missing file");
        }
        let fd = new FormData();
        fd.append("key","DALImageUpload");
        fd.append("image",input.files[0]);
        SYS.xhr_post(null,fd,"text","no",function(r,o){
            console.log(r);
            r = JSON.parse(r);
            if(r.result == true){
                $(that).parents(".form-group").find("input[type='hidden']").val(r.name);
                $(that).parents(".form-group").find(".image-view-thumbnail").html(`
                    <img height=50 src="${r.url}" />
                    <p>this is a thumbnail, 
                     <a href="${r.url}" target="_blank" > see full image </a> </p>
                `);
            }
            else{
                $(that).parents(".form-group").find(".image-view-thumbnail").html(`${r.msg}`);
            }
            
        });
    }
    static dialog(message,title="Notice",color="grey"){
        alert(message);
        return;
        $(`<div title="${title}" >${message}</div>`).dialog({
            modal: true,
            open: function(){
                //jQuery('.ui-dialog-titlebar').css("background",color);
                jQuery('.ui-dialog-titlebar-close')
                .html(`<i class="fa fa-window-close"></i>`)
                .css("padding",0).css("border","none")
                ;
                //var closeBtn = jQuery('.ui-dialog-titlebar-close'); //fix dialog x button display
                //closeBtn.html('<i class="fa fa-eye"></i>');
              },
            buttons: {
              OK: function() {
                $( this ).dialog( "close" );
              }
            }
        });
    }
    
    static calender_setup(target,_events = []){
        let to = $(target).attr("to");
        var calendar = $(target).fullCalendar({
            editable: true,
            events: _events,
            displayEventTime: false,
            eventRender: function (event, element, view) {
                if (event.allDay === 'true') {
                    event.allDay = true;
                } else {
                    event.allDay = false;
                }
            },
            selectable: true,
            selectHelper: true,
            select: function (start, end, allDay) {
                var start = $.fullCalendar.formatDate(start, "Y-MM-DD HH:mm:ss");
                var end = $.fullCalendar.formatDate(end, "Y-MM-DD HH:mm:ss");
                var title = prompt('Event Title:');
                if (title) {
                    let fd = new FormData();
                    fd.append("key","add_event");
                    fd.append("title",title);
                    fd.append("start",start);
                    fd.append("end",end);
                    SYS.xhr_post(null,fd,"text","no",function(r,o){
                        $(`#${to}`).html(r);
                        let ev = {
                            title: title,
                            start: start,
                            end: end,
                            allDay: allDay
                        };
                        calendar.fullCalendar('renderEvent',ev,true);
                    });
                }
                calendar.fullCalendar('unselect');
            },
            editable: true,
            eventDrop: function (event, delta) {
                //var start = $.fullCalendar.formatDate(event.start, "Y-MM-DD HH:mm:ss");
                //var end = $.fullCalendar.formatDate(event.end, "Y-MM-DD HH:mm:ss");
                // $.ajax({
                //     url: `${BASE_DIR}/profile/edit_event`,
                //     data: 'title=' + event.title + '&start=' + start + '&end=' + end + '&id=' + event.id,
                //     type: "POST",
                //     success: function (response) {
                //         alert(response);
                //     }
                // });
            },
            eventClick: function (event) {
                let id = event.id;
                //alert("event id = " + id);
            }
        });
    }
}