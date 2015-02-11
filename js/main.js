// All Rights Reserved
// By: Romnick Bien Pantua (pantuts)
// MIT License


$(function(){

    // form submission cache set to false
    $.ajaxSetup({ cache: false });
    
    // clone the download, delete, send
    var cloneDDS = $('ul.dds').children().clone(true);
    
    // name of selected file
    var selectedFile = '';

    // variables to store json encoded links by php
    var downloadLink = '';
    var deleteLink = '';

    // check file input change, file size check, and then upload
    function makeUpload(){

        $('input:file').on('change', function(e) {
            
            // the filename
            selectedFile = this.files[0].name;
            
            // set the DDS to normal again if any changed has been made and then clone again
            $('ul.dds').empty();
            $('ul.dds').html(cloneDDS);
            cloneDDS = $('ul.dds').children().clone(true);
            
            // the filesize
            $fs = this.files[0].size;
            $maxFileSize = parseInt($('input:hidden').attr('value'));

            // checking the filesize of file
            if($fs > $maxFileSize){
                $('span.hidden').hide();
                $('span.error').stop().fadeIn('slow').show();
                $('span.tooltip').hide();
                $('input:file').val('');
                $('#upload-form').resetForm();
                return false;
            }
            else{
                $('span.hidden').hide();
                submitAndTrack();
                return false;
            }
            return false;
        });
    }
    makeUpload();

    // jsonError parsing encoded by php
    function parseError(data){
        var obj = jQuery.parseJSON(data.responseText);
        return obj.error;
        // .error comes from php json encoded msg { 'error': 'something error' }
    }

    // use the plugin form.min.js to submit and track upload progress
    function submitAndTrack(){
        
        $('#upload-form').ajaxSubmit({
            beforeSend: function() {
                var percentVal = '0%';
                $('span.progress-bar').css({ 'width':percentVal, 'display':'block' });
            },
            uploadProgress: function(event, position, total, percentComplete) {
                var percentVal = percentComplete + '%';
                $('span.progress-bar').css({ 'width':percentVal, 'display':'block' });
            },
            success: function(data) {
                var percentVal = '100%';
                $('span.progress-bar').css({ 'width':percentVal, 'display':'block' });
                $('span.upload-success').text('File successfully uploaded.').stop().fadeIn('slow').show();
                // $('#upload-form').resetForm();
                setActiveDDS();
                hoverActiveDDS();
                // download, delete comes from json_encoded server response
                downloadLink = data.download;
                deleteLink = data.delete;
                
                // the copy download event, delete event and send event
                // we only make event available here in success submission
                
                // copy download link
                $('.download-active').on('click', function(){
                    copyToClipboard(downloadLink);
                });
                
                // delete click
                deleteClick();
                
                // send click
                sendClick();
                
                // console.log(data.download + '\n' + data.delete);         
                return false;
            },
            complete: function(xhr) {
                $('span.progress-bar').hide();
            },  
            error: function(data) {
                $('span.hidden').hide();
                $('span.error').text(parseError(data)).stop().fadeIn('fast').show();
                $('span.tooltip').hide();
                $('#upload-form').resetForm();
                // console.log(data);
                return false;
            }
        });
        return false;
    }

    // set download, delete, send button to active
    var setActiveDDS = function(){
        $('.download').addClass('download-active').removeClass('download');
        $('.delete').addClass('delete-active').removeClass('delete');
        $('.send').addClass('send-active').removeClass('send');
        $('.li-dds').css({ 'cursor': 'pointer' });
        return false;
    };

    // hover effect on activeDDS and then show the tooltip on each class if set in config
    var hoverActiveDDS = function(){

        $('span.tooltip').hide();

        $('.download-active, .delete-active, .send-active').hover(function() {
            $(this).stop().animate({
                'opacity': '0.7'
            }, 'fast', function(){

                // if tip = show in config.ini
                var tipvalue = $('span.tipvalue').text();

                if(tipvalue == 'show'){
                    // check if current li:hover has a className accordingly
                    if($(this).hasClass('download-active')){
                        $('span.tooltip').stop().fadeIn('fast').show().css({ 'opacity': '0.7' });
                        showTip('Copy download link.', $(this));
                    }
                    else if($(this).hasClass('delete-active')){
                        $('span.tooltip').stop().fadeIn('fast').show().css({ 'opacity': '0.7' });
                        showTip('Delete file.', $(this));
                    }
                    else if($(this).hasClass('send-active')){
                        $('span.tooltip').stop().fadeIn('fast').show().css({ 'opacity': '0.7' });
                        showTip('Send link.', $(this));
                    }
                }
                
            });
        }, function() {
            // back to normal
            $(this).stop().animate({
                'opacity': '1'
            }, 'fast', function(){
                $('.tooltip').stop().fadeOut('fast').hide();
            });
        });
        return false;
    };

    // the tooltip
    function showTip(tip, el){
        // set tooltip next to its element
        var w = el.outerWidth();
        var pos = el.position();
        $('span.tooltip').html(tip);
        $('span.tooltip').css({ top: pos.top + 140 + 'px', left: (pos.left + w + 10) + 'px' });
        return false;
    }

    // copy download link to clipboard workaround
    function copyToClipboard (text) {
        window.prompt ("Copy to clipboard:", text);
        return false;
    }
    
    // delete click event
    function deleteClick(){
        
        $('.delete-active').bind('click', function(event){
            // we use ajax call to send event to php
            $.ajax({
                type: 'POST',
                url: './ayedelete.php',
                data: 'fileDelete=' + deleteLink, // deleteLink: global var made by success upload
                success: function(data){
                    $('span.hidden').hide();
                    $('.tooltip').stop().fadeOut('fast').hide();
                    // we use span.upload-success but substitute the text to confirm delete complete
                    $('span.upload-success').text('File deleted.').show();
                    $('ul.dds').empty();
                    $('ul.dds').html(cloneDDS);
                    // clone again the default DDS
                    cloneDDS = $('ul.dds').children().clone(true);
                    $('#upload-form').resetForm();
                    // console.log(data['responseText']);
                    return false;
                },
                error: function(data){
                    $('span.hidden').hide();
                    $('span.error').text(parseError(data)).stop().fadeIn('fast').show();
                    // console.log(data['responseText']);
                    return false;
                }
            });
            return false;
        });
    }
    
    // send click event
    function sendClick(){
        
        $('.send-active').bind('click', function(event){
            
            var mailing = $('.mailing');
            // lets make the div.mailing as full transparent background
            mailing.fadeIn()
            .css({ 'display':'block' })
            .css({ width: $(window).width() + 'px', height: $(window).height() + 'px' })
            .css({ top:($(window).height() - mailing.height())/2 + 'px', left:($(window).width() - mailing.width())/2 + 'px', 'background-color': 'rgba(0,0,0,0.6)' })
            .appendTo('body');
            
            // center the mail form
            $('#mail-form').css({ top: ($(window).height() - $('#mail-form').height())/2 + 'px', left: '0', bottom: '0', right: '0' });
            
            // exit modal
            $('span.close').click(function(){
                mailing.fadeOut();
            });
            
            // clear the fields first
            function clearFields(){
                $('input#email').val("mail@mail.com");
                $('input#subject').val("");
                $('textarea#message').val("");
                return false;
            }
            $('span.send-response').text("").hide();
            clearFields();
            
            // put the subject
            $('input#subject').val('Download: ' + selectedFile);
            
            // put the download link on textarea
            $('textarea#message').val('Link: ' + downloadLink + '\n\nClick the download link or copy to download the file.');
            
            // send action
            $('span.send-mail').bind('click', function(){
                
                var email = $('input[name="email"]').val();
                var subj = $('input[name="subject"]').val();
                var message = $('textarea').val();
                var e_validate = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
                
                // validate the email
                if( !e_validate.test(email) ){
                    $('span.send-response').text('Please check your email.').css({ 'color':'red' }).stop().show();
                    return false;
                }
                // check if the message has 100 characters long to see if the download link is attached
                else if( message.length === 0 || message < 100 ){
                    $('span.send-response').text('Your too sweet for that message, change it!').css({ 'color':'red' }).stop().show();
                    return false;
                }
                else{
                    
                    // if subj is empty then default to No Subject for mailing
                    if( subj.length === 0 ) { subj = "No Subject"; }
                    
                    // perform ajax event
                    $.ajax({
                        type: 'POST',
                        url: './ayesend.php',
                        data: $('#mail-form').serialize(),
                        success: function(data){
                            $('span.send-response').text('Mail successfully sent.').css({ 'color':'#333' }).stop().show();
                            $('#mail-form').find('input#email').val("New email?");
                            clearFields();
                            // console.log(data['responseText']);
                            return false;
                        },
                        error: function(data){
                            $('span.send-response').text('Error encountered sending email.').css({ 'color':'red' }).stop().show();
                            // console.log(data['responseText']);
                            return false;
                        }
                    });
                    return false;
                }
            });
            
            return false;
            
        });
    }

});
