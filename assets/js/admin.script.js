/* eslint-env browser */
(function () {
    
    'use strict';
    var i, btn_toggle, remove_links;
    
    btn_toggle = document.getElementsByClassName('toggle-icon');
    
    function toggleClick(e) {
        
        var btn, btnId, checkbox, is_off;
        
        btn = e.currentTarget;
        btnId = btn.getAttribute('data-id');
        checkbox = document.getElementById(btnId);
        is_off = btn.classList.contains('off');

        if (is_off === true) {
            
            btn.classList.remove('off');
            checkbox.checked = true;
            
        } else {
            
            btn.classList.add('off');
            checkbox.checked = false;
            
        }
        
    }
    
    function deleteCommentRefsData(e) {
        
        var xhr, deleted_comment, deleted_comment_id, delete_comment_refs;
        
        xhr = e.currentTarget;
        
        if(xhr.readyState === 4 && xhr.DONE === 4) {
            
            if(xhr.status === 200) {
                deleted_comment = JSON.parse(xhr.responseText);
                
                if (deleted_comment.success === true && deleted_comment.data.comment_id !== '') {
                    
                    deleted_comment_id = deleted_comment.data.comment_id;
                    delete_comment_refs = document.getElementById('crefs-comment-' + deleted_comment_id);
                    delete_comment_refs.innerHTML = '';
                    delete_comment_refs.style.display = 'none';
                }

            }
            
        }
        
    }
    
    function generateLoadingIcon(comment_id) {
        var current_comment;
        
        current_comment = document.getElementById('comment-loading-id-' + comment_id);
        current_comment.style.display = 'inline-block';
    }
    
    function setupAjaxRequest(params) {
        var xhr;
        xhr = new XMLHttpRequest();
        xhr.open('POST', document.location.origin + '/wp-admin/admin-ajax.php?action=crefs_remove_comment_refs');
        xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
        xhr.onreadystatechange = deleteCommentRefsData;
        xhr.send(params);
    }
    
    
    function removeCommentRefs(e) {
        var remove_link, comment_id, wpnonce, params;
        
        e.preventDefault();
        e.stopPropagation();
        
        remove_link = e.currentTarget;
        comment_id  = remove_link.getAttribute('data-comment-id');
        wpnonce = remove_link.getAttribute('data-nonce');
        params = encodeURI('comment_id=' + comment_id + '&nonce=' + wpnonce);
        
        generateLoadingIcon(comment_id);
        setupAjaxRequest(params);
        
    }
    
    if (btn_toggle !== null) {
        for (i = 0; i < btn_toggle.length; i += 1) {
            btn_toggle[i].addEventListener('click', toggleClick, false);
        }
    }
    
    remove_links = document.getElementsByClassName('crefs_remove_link');
    
    if (remove_links !== null) {
        
        for (i = 0; i < remove_links.length; i += 1) {
            
            remove_links[i].addEventListener('click', removeCommentRefs, false);
        }
        
    }
    
}());