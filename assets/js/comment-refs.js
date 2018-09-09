/* eslint-env browser */
/* global crefs_api_url */

(function () {
	'use strict';
	var i, api_settings, get_ten, get_dofollow, lqc_check, sm_integration,
        form_element, website_input, comment_textarea, btn_submit,
        conent_wrap, crefs_posts;
    
    api_settings = crefs_api_url.general;
    get_ten = crefs_api_url.get_ten;
    get_dofollow = crefs_api_url.get_dofollow;
    lqc_check = crefs_api_url.prevent_lqc;
    sm_integration = crefs_api_url.sm_integration;
    
    form_element = document.querySelectorAll('#commentform input');
    comment_textarea = document.querySelector('textarea[name="comment"]');
    website_input = document.querySelector('#commentform input[name="url"]');
    btn_submit = document.querySelector('input[name="submit"]');
    conent_wrap = document.getElementById('crefs-content-wrap');
    
    /***********************************************
     * Helper functions
     ************************************************/
	function createElement(el) {
		return document.createElement(el);
	}
	
	function setAttributes(el, attrs) {
        
		if (Array.isArray(attrs)) {
            
			var i, attr;
            
			for (i = 0; i < attrs.length; i += 1) {
                
				if ((i + 1) % 2 === 1) {
                    
					attr = document.createAttribute(attrs[i]);
                    
				} else {
                    
					attr.value = attrs[i];
                    
				}
                
				el.setAttributeNode(attr);
                
			}
            
			return el;
            
		}
        
	}
	
	function generateElements(el, attrs) {
        
		el = createElement(el);
        
		return setAttributes(el, attrs);
	}
    
    /*
     * Generate spinner loading icon inside CommentRefs wrapper 
     */
    
    function generateLoadingIcon() {
        
        var spinner, bar, conent_wrap;
        conent_wrap = document.getElementById('crefs-content-wrap');
        
        if (conent_wrap !== null) {
            
            conent_wrap.innerText = '';
            spinner = generateElements('div', ['class', 'spinner']);
            spinner.innerText = 'loading data';
            bar = generateElements('img', ['src', api_settings.plugin_url + 'assets/images/loading-bar-64px.gif']);
            
            spinner.prepend(bar);
            conent_wrap.append(spinner);
        }
    }
    
    /*
     * Disable element on event call
     */
    
    function preventEventPropagation(e) {
        
        e.preventDefault();
        e.stopPropagation();
        
    }
    
    /*
     * Generate social icons before inside CommentRefs items list.
     */
    
    function generateShareButtons() {
        
        var i, platforms, wraper, share_button, share_icon, share_button_content, share_buttons, get_ten_message, posts_list;
        
        posts_list = document.getElementById('crefs-item-list');
        platforms = ['facebook', 'twitter', 'google', 'linkedin'];
        
        /* generate wrapper element for social icons*/
        
        wraper = generateElements('span', ['class', 'crefs-share crefs-share-wraper']);
        
        /* loop in social platforms and generate share button for each platforms */
        
        for (i = 0; i < platforms.length; i += 1) {
            
            /* skip generating facebook button if App ID is empty */
            
            if (platforms[i] === 'facebook' && sm_integration.facebook_app === '') {
                
                continue;
                
            }
            
            
            share_button = generateElements('span', ['id', platforms[i], 'class', 'crefs-share-button']);
            
            share_icon = generateElements('img', ['src', api_settings.plugin_url + 'assets/images/' + platforms[i] + '.png']);
            
            share_button_content = generateElements('a', ['id', platforms[i], 'href', '#', 'class', 'crefs-share-btn btn-' + platforms[i], 'data-platform', platforms[i]]);
            share_button_content.innerText = platforms[i];
            share_button_content.prepend(share_icon);
            
            share_button.append(share_button_content);
            
            wraper.append(share_button);
        }
        
        /* prepend the social buttons to CommentRefs items list*/
        if (posts_list !== null) {
            
            posts_list.prepend(wraper);
            
        }
        
        /* Add event listener to share buttons */
        share_buttons = document.getElementsByClassName('crefs-share-btn');
        
        for (i = 0; i < share_buttons.length; i += 1) {
            
            share_buttons[i].addEventListener('click', sharePost, false);
        }
    }
    
    function displayMessages(target) {
        
        var i, messages, messages_wrap, messages_list, message;
        
        messages = [get_ten.message, get_dofollow.message];
        
        if (api_settings.credit_message !== '') {
            messages.push(api_settings.credit_message);
        }
        
        messages_wrap = generateElements('div', ['id', 'crefs-messages-wrap']);
        messages_wrap.innerHTML = '<span><strong>Notice:</strong></span>';
        messages_list = generateElements('ul', ['class', 'messages-list']);
        
        for (i = 0; i < messages.length; i += 1) {
            
            message = generateElements('li', ['class', 'crefs-message']);
            message.innerHTML = messages[i];
            messages_list.append(message);
            
        }
        
        messages_wrap.append(messages_list);
        target.append(messages_wrap);
        
    }
    
    /*
     * Display error message and dissable comment button
     */
    
    function displayErrors(errs) {
        
        var error_element, btn_close, append_target;
        
        btn_submit.addEventListener('click', preventEventPropagation, false);
        
        error_element = generateElements('span', ['class', 'crefs-error crefs-' + errs]);
        
        switch (errs) {
                
        case 'low_quality':
            error_element.innerText = 'The comment does not comply with comment policies';
            break;
                
        case 'invalid_url':
            error_element.innerText = 'The provided URL is incorrect or invalid format';
            break;
                
        case 'api_disconnected':
            error_element.innerText = 'Cannot communicate with WordPress API (Network error or CROS trouble)';
            break;
                
        }
        
        if (typeof (error_element) !== 'undefined') {
            
            append_target = document.getElementById('crefs-content-wrap');
            append_target.innerHTML = '';
            
            addEnableRecommentRefsCheckbox();
            
            btn_close = generateElements('span', ['id', 'btn-close-err']);
            btn_close.innerText = 'X';
            
            error_element.append(btn_close);
            
            append_target.append(error_element);
            
            btn_close = document.getElementById('btn-close-err');
            
            if (btn_close !== null) {
                
                btn_close.addEventListener('click', function(e){
                    
                    var content_wrap;
                    
                    preventEventPropagation(e);
                    
                    btn_submit.removeEventListener('click', preventEventPropagation, false);
                    
                    content_wrap = document.getElementById('crefs-content-wrap');
                    
                    if (content_wrap !== null) {
                        
                        content_wrap.removeChild(e.currentTarget.parentNode);
                        
                    }
                    
                }, false);
                
            }
            
        }
        
    }
    
    /*
     * Check low quality comments if setting is configure
     * return boolean
     */
    
    function isLowQualityComment() {
        
        var low_quality_comment, comment_words, regexp, matches;
        
        low_quality_comment = false;
        
        /* Check if prevent short comment is set to on */
        
        if (lqc_check.prevent_short_comment === 'on') {
            
            /**/
            if (comment_textarea.value === '') {
                
                low_quality_comment = true;
                
                return low_quality_comment;
                
            } else {
                
                comment_words = comment_textarea.value.split(' ');
                
                if (comment_words.length < lqc_check.minimum_length) {
                    
                    low_quality_comment = true;
                    return low_quality_comment;
                }
                
            }
            
        }

        /* return true if the comment contain link */
        if (lqc_check.prevent_link_in_comment === 'on') {
            
            regexp = /<a.*>/gm;
            
            matches = regexp.exec(comment_textarea.value);
            
            if (matches !== null) {
                
                low_quality_comment = true;
                
            }
            
        }
        
        return low_quality_comment;
    }
    
    function handleErrosCommentRequest(e) {
        var xhr;
        
        xhr = e.currentTarget;
        xhr.returnValue = false;
    }
    
    function handleSuccessfullCommentRequest(e) {
        
        preventEventPropagation(e);
        
        var xhr, response_comments;
        
        xhr = e.currentTarget;
        
        if (xhr.readyState === 4 && xhr.status === 200) {
            
            response_comments = JSON.parse(xhr.responseText);
            
            if (response_comments.success === true ) {
                
                xhr.returnValue = response_comments.data.previus_comments;
                
            } else {
                
                xhr.returnValue = 0;
            }
            
        }
        
    }
    /*
     * Check if user can get 10 post in the list
     * return boolean
     */
    
    function isUserCanGetTenPosts() {
        
        if (get_ten !== null) {
            
            if (api_settings.is_admin === true) {
                
                return true;
                
            } else if (get_ten.everyone === "on") {
                
                return true;
                
            } else if (api_settings.loggedin === true && get_ten.registered_the_site === "on") {
                
                return true;
                
            } else if (api_settings.is_author === true && get_ten.post_author === "on") {
                
                return true;
                
            } else if (get_ten.has_previous_comments === "on") {
                
                if (api_settings.loggedin === true && api_settings.comments > get_ten.minimum_comments) {
                    
                    return true;
                    
                } else {
                    
                    var api_url, comment_author_email, previous_comment_counts;
                    
                    comment_author_email = document.querySelector('input[name="email"]');
                    
                    if (comment_author_email !== null && comment_author_email.value !== '') {
                        
                        api_url = document.location.origin + '/wp-admin/admin-ajax.php?action=crefs_get_comment_count&author_email=' + encodeURI(comment_author_email.value) + '&wp_nonce=' + api_settings.wponce;
                        previous_comment_counts = setupAjaxRequest(api_url, handleErrosCommentRequest, handleSuccessfullCommentRequest, false);
                        
                        if (previous_comment_counts > get_ten.minimum_comments) {
                            return true;
                        }
                        
                    }
                    
                    return false;
                    
                }
                
                
            } else if (get_ten.shared_the_post === "on" && get_ten.shared_post === true) {
                
                return true;
            }
        }
        
        return false;
    }
    
    /*
     * Check if the website is valid url.
     * return url or false
     */
    
    function validateUrl(url) {
        
        var regexp, match;
        
        regexp = /^(https?\:\/\/)(w{3}\.)?([^\.]+\S)(\.+[a-z]{2,8})$/;
        
        match = regexp.exec(url);
        
        url = (match !== null && match[0] !== null) ? match[0] : false;
        
        return url;
    }
    
    /*
     * Get website url and generate front-end request url
     */
    
    function generateAPIURL(args) {
        var url, uniquecode;
        
        if (args.website !== "") {
            
            url = validateUrl(args.website);
            
        } else if (website_input.value !== null) {
            
            url = validateUrl(website_input.value);
        }
    
        if (url) {
            uniquecode = new Date().getTime();
            uniquecode = encodeURI(uniquecode);
            return url + '/wp-json/wp/v2/posts?uniquecode=' + uniquecode;
            
        } else {
            
            return false;
        }
	}
	
    /*
     * Generate input fields for CommentRefs metas
     */
	function addMetaInputs() {
        
		var comment_form, title_field, post_url_field, wponce_field, metas_container;
        
        metas_container = document.getElementById('crefs-metas-container');
        
        if (metas_container === null) {
            
            comment_form = document.getElementById('commentform');
            
            metas_container = generateElements('div', ['id', 'crefs-metas-container']);
            
            title_field = generateElements('input', ['type', 'hidden', 'name', 'comment_refs_metas[title]', 'id', 'comment_refs_metas_title']);
            post_url_field = generateElements('input', ['type', 'hidden', 'name', 'comment_refs_metas[url]', 'id', 'comment_refs_metas_url']);
            wponce_field = generateElements('input', ['type', 'hidden', 'name', 'comment_refs_wponce', 'id', 'comment_refs_wponce']);

            metas_container.append(title_field);
            metas_container.append(post_url_field);
            metas_container.append(wponce_field);
            comment_form.append(metas_container);
        }
    }
    
    /*
     * Generate  CommentRefs checkbox
     */
   
    function addEnableRecommentRefsCheckbox() {
        
        var checkbox, checkbox_label, content_wrap, error_message, comment_form;
        
        comment_form = document.getElementById('commentform');
        
        content_wrap = document.getElementById('crefs-content-wrap');
        
        checkbox = document.getElementById('enable_commentrefs');
        
        if (content_wrap === null) {
            
            content_wrap = generateElements('p', ['id', 'crefs-content-wrap']);
            comment_form.append(content_wrap);
            
        }
        
        if (checkbox === null) {
            
            checkbox_label = generateElements('label', ['for', 'enable_commentrefs']);
            checkbox_label.innerText = 'Enable CommentRef';
            checkbox = generateElements('input', ['type', 'checkbox', 'name', 'enable_commentrefs', 'id', 'enable_commentrefs']);
            checkbox_label.prepend(checkbox);
            content_wrap.prepend(checkbox_label);
            
        }
        
    }
    
    /*
     * Append CommentRefs checkbox and input fields
     */
    function setupCommentRefs(e) {
        
        var comment_refs_checkbox;
        
        preventEventPropagation(e);
        
        /* append enable CommentRefs Checkbox underneth the comment form */
        addEnableRecommentRefsCheckbox();
        
        /* Append CommentRefs meta input form elements underneth the commment form */
        addMetaInputs();
        
        comment_refs_checkbox = document.querySelector('label[for="enable_commentrefs"]');
        
        /* Handle Ajax request when user clicks on enable CommentRefs checkbox */
        if (comment_refs_checkbox !== null) {
            
            comment_refs_checkbox.addEventListener('click', function (e) {

                var checkbox, url;

                e.stopPropagation();

                checkbox = e.currentTarget.children;
                checkbox = checkbox[0];
                checkbox.checked = !checkbox.checked;

                if (checkbox.checked) {

                    url = generateAPIURL(api_settings);

                    if (url) {

                        if (isLowQualityComment() === false) {

                            generateLoadingIcon();

                            setupAjaxRequest(url, handlePostErrorRequest, handlePostSuccessfulRequest);

                        } else {

                            btn_submit.addEventListener('click', preventEventPropagation, false);
                            displayErrors('low_quality');

                        }

                    } else {

                        btn_submit.addEventListener('click', preventEventPropagation, false);
                        displayErrors('invalid_url');

                    }

                }

            }, false);
            
        }
        
    }

    /*
     * Generate popup window for social share
     */
    
    function sharePost(e) {
        
        var target_element, platform, meta_container, post_url, post_title, timmer, shareUrl, popup, facebook, twitter, google, linkedin;
        
        preventEventPropagation(e);
        
        target_element = e.currentTarget;
        
        post_title = encodeURI(api_settings.title);
        
        post_url = encodeURI(api_settings.permalink);
        
        platform = target_element.getAttribute('data-platform');
        
        meta_container = document.getElementById('crefs-metas-container');
        
        switch (platform) {
                
        case 'facebook':
                
            facebook = generateElements('input', ['type', 'hidden', 'name', 'comment_refs_metas[shared_on][]', 'value', platform, 'id', 'comment_refs_metas_platform']);
            meta_container.append(facebook);
            shareUrl = 'https://www.facebook.com/dialog/feed?app_id=' + sm_integration.facebook_app + '&display=popup&name=' + post_title + '&link=' + post_url;
            break;

        case 'twitter':
                
            twitter = generateElements('input', ['type', 'hidden', 'name', 'comment_refs_metas[shared_on][]', 'value', platform, 'id', 'comment_refs_metas_platform']);
            meta_container.append(twitter);
            shareUrl = 'https://twitter.com/intent/tweet?text=' + post_title + '&url=' + post_url;
            break;

        case 'linkedin':
                
            linkedin = generateElements('input', ['type', 'hidden', 'name', 'comment_refs_metas[shared_on][]', 'value', platform, 'id', 'comment_refs_metas_platform']);
            meta_container.append(linkedin);
            shareUrl = 'https://www.linkedin.com/sharing/share-offsite/?url=' + post_title;
            break;

        case 'google':
                
            google = generateElements('input', ['type', 'hidden', 'name', 'comment_refs_metas[shared_on][]', 'value', platform, 'id', 'comment_refs_metas_platform']);
            meta_container.append(google);
            shareUrl = 'https://plus.google.com/share?url=' + post_url;
            break;
        }
    
        generateLoadingIcon();
        
        popup = window.open(shareUrl, '_blank', 'width=360px, height=480px, left=10px, scrollbars=no');
        
        timmer = setInterval(function () {
            
            if (popup.closed) {
                
                clearInterval(timmer);
                
                if (get_ten.shared_the_post === 'on' || isUserCanGetTenPosts() === true) {
                    
                    displayPosts(crefs_posts, true);
                    
                } else {
                    
                    displayPosts(crefs_posts, false);
                }
            }
            
        });
        
    }

    /*
     * Update CommentRef metas and items list
     */
    
	function selectPost(e) {
        
        var selected_post, checkbox, meta_title, meta_url, meta_wpnone, items_list, post;
        
        preventEventPropagation(e);
        
        selected_post = e.currentTarget;
        
        checkbox = document.getElementById('enable_commentrefs');
        meta_title = document.getElementById('comment_refs_metas_title');
        meta_url = document.getElementById('comment_refs_metas_url');
        meta_wpnone = document.getElementById('comment_refs_wponce');
        items_list = document.getElementById('crefs-item-list');
        post = generateElements('li', ['class', 'crefs-item selected', 'data-toggle', 'true']);
        
        checkbox.checked = true;
        meta_url.value = selected_post.href;
        meta_wpnone.value = api_settings.wponce;
        meta_title.value = selected_post.innerText;
        
        items_list.innerHTML = '';
        items_list.classList.add('selected');
        post.append(selected_post);
        items_list.append(post);
        
        selected_post.addEventListener('click', function () {
            
            displayPosts(crefs_posts, isUserCanGetTenPosts());
            
        }, false);
        
        btn_submit.removeEventListener('click', preventEventPropagation, false);
        
	}
	
    /*
     * Display post list when successful Ajax request
     */
	function displayPosts(posts, get_ten_posts) {
        
		var i, items_list, item, post;
        
		conent_wrap = document.getElementById('crefs-content-wrap');
		conent_wrap.innerHTML = '';
        
		items_list = generateElements('ul', ['id', 'crefs-item-list']);
        
        
		for (i = 0; i < posts.length; i += 1) {
            
			post = '<a id="' + posts[i].id + '" class="crefs-post"' + ' href="' + posts[i].link + '" title="' + posts[i].title.rendered + '"' + '>' + posts[i].title.rendered + '</a>';
            
            if (get_ten_posts === false && i >= 1) {
                
                item = generateElements('li', ['class', 'crefs-item inactive']);
                
            } else {
                
                item = generateElements('li', ['class', 'crefs-item active']);
                
            }
            
            item.innerHTML = post;
            
			items_list.append(item);
            
		}
        
        
		addEnableRecommentRefsCheckbox();
        
        conent_wrap.append(items_list);
        
        items_list = document.getElementById('crefs-item-list');
        
        if (items_list !== null && isUserCanGetTenPosts() === false) {
            
            displayMessages(items_list);
            
        }
        
        
		post = document.getElementsByClassName('crefs-post');
        
        for (i = 0; i < post.length; i += 1) {
            
            item = post[i].parentElement;
            
            if (item.classList.contains('active')) {
                
                post[i].addEventListener('click', selectPost, false);
                
            } else {
                
                post[i].addEventListener('click', preventEventPropagation, false);
            }
        }
        
        generateShareButtons();
    }
    
    /*
     * Handle successful Ajax response
     */
    
    function handlePostSuccessfulRequest(e) {
        
        var xhr;
        
        preventEventPropagation(e);
        
        xhr = e.currentTarget;
        
        if (xhr.readyState === 4 && xhr.DONE === 4) {
            
            if (xhr.status === 200) {
                
                crefs_posts = JSON.parse(xhr.responseText);
                
                if (isUserCanGetTenPosts() === true) {
                    
                    displayPosts(crefs_posts, true);
                    
                } else {
                    
                    displayPosts(crefs_posts, false);
                    
                }
                
            }
            
        }
        
    }
    
    /*
     * Handle Ajax error and display error message
     */
    
    function handlePostErrorRequest(e) {
        
        preventEventPropagation(e);
        
        displayErrors('api_disconnected');
        
    }
    
    /*
     * Setup Ajax request
     */
    
	function setupAjaxRequest(url, errorHandler, responseHandler) {
        
		var xhr, checkbox, boolean_return;
        checkbox = document.getElementById('enable_commentrefs');
        
        if (checkbox !== null) {
            
            checkbox.checked = true;
            
        }
        
        xhr = new XMLHttpRequest();
        
        //xhr.returnValue;
		xhr.onreadystatechange = responseHandler;
        xhr.onerror = errorHandler;
		xhr.open('GET', url, true);
        xhr.setRequestHeader('Content-Type', 'application/json, application/commentrefs; charset=UTF-8');
		xhr.send();
        
        return xhr.returnValue;
        
	}
    
    /*
     * Stop event propagation on comment button to prevent accidently trigging selectPost
     */
    
    if (btn_submit !== null) {
        
        btn_submit.addEventListener('click', function (e) {
            
            e.stopPropagation();
            
        });
        
    }
    
    /*
     * Setup CommentRefs meta inputs when user focus on form elements.
     */
    
    if (form_element !== null && form_element.length > 0) {
        
        for (i = 0; i < form_element.length; i += 1) {
            
            if (form_element[i].getAttribute('type') !== 'hidden' && form_element[i].getAttribute('type') !== 'submit') {
                
                form_element[i].addEventListener('focus', setupCommentRefs, false);
                
            }
            
        }
        
    }
    
    if (comment_textarea !== null) {
        
        /*
         * Setup CommentRefs element when focus on the comment are
         */
        comment_textarea.addEventListener('focus', setupCommentRefs, false);
        
        /*
         * Handle Ajax request on blur even of the comment area
         */
        comment_textarea.addEventListener('blur', function (e) {
            
            var url;
            
            /* stop event propagation and prevent default for the event */
           // preventEventPropagation(e)
            
            /* Check low quality comment if the settings is configure */
            if (isLowQualityComment() === false) {
                
                /* 
                 * Check if the URL input is null or user is login
                 * if so, get URL and setup CommentRefs
                 */
                
                if (website_input === null) {
                    
                    url = generateAPIURL(api_settings);
                    
                    if (url) {
                        
                        setupCommentRefs(e);
                        
                        generateLoadingIcon();
                        
                        setupAjaxRequest(url, handlePostErrorRequest, handlePostSuccessfulRequest);
                        
                    } else {
                        
                        btn_submit.addEventListener('click', preventEventPropagation, false);
                        
                        displayErrors('invalid_url');
                    }
                    
                }
                
            } else {
                
                /* Handle error message if the comment isn't enough quality */
                
                btn_submit.addEventListener('click', preventEventPropagation, false);
                displayErrors('low_quality');
            }
            
        }, false);
        
    }
    
    /*
     * Handle Ajax request on blur even of the website input field
     */
    
    if (website_input !== null && comment_textarea.value !== '') {
        
        website_input.addEventListener('blur', function (e) {
            
            var url;
            
            preventEventPropagation(e);
            
            url = generateAPIURL(api_settings);
            
            /* check low quality comment if setting is configured */
            
            if (isLowQualityComment() === false) {
                
                if (url) {
                    
                    setupCommentRefs(e);
                    
                    generateLoadingIcon();
                    
                    setupAjaxRequest(url, handlePostErrorRequest, handlePostSuccessfulRequest);
                    
                } else {
                    
                    /* Handle Error message if the website is invalid format */
                    
                    btn_submit.addEventListener('click', preventEventPropagation, false);
                    displayErrors('invalid_url');
                    
                }
                
            } else {
                
                /* Handle error message if the comment isn't enough quality */
                
                btn_submit.addEventListener('click', preventEventPropagation, false);
                displayErrors('low_quality');
            }
            
        }, false);
        
    }

}());