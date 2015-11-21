

	var clickEvent = (function () {

		var userLogin,
			praiseQue;

		userLogin = function () {
            if (util.checkUserInfo($(this).parents('form'))) {
				var $button = $(this);
	            $.ajax({
	            	url: 'index.php?s=/Mobile/User/userLogin',
	            	type: 'POST',
	            	dataType: 'json',
	            	data: $('#user-login-form').serialize(),
	            	beforeSend: function () {
	            		$button.button('loading');
	            	}
	            }).done(function (response, status) {
	            	if (response.status === 200 && status === 'success') {
	            		location.href = response.data;
	            	} else {
	            		util.alert("你的账号或密码有误");
	            	}
	            }).fail(function (jqXHR, textStatus) {
	            	util.alert('稍安勿躁, 好像出了点小问题=_=');
	            }).always(function() {
	            	setTimeout(function () {
	            		$button.button('reset');
	            	}, 2000);
	            });
            } else {
            	util.alert('用户名或密码输入有误~');
            }
		}

		praiseQue = function () {
			if ($.AMUI.utils.cookie.get('userId')) {
				alert();
			} else {
				util.confirm('点赞或评论是需要登录哦~');
			}
		}

		return {
			userLogin: userLogin,
			praiseQue: praiseQue
		};
	})();



	var ajax = (function () {






	})();


	var util = (function () {

		var checkUserInfo;

		var alert,
			confirm;

		checkUserInfo = function (form) {
			var userName = $.trim(form.find('input[name=user-name]').val());
			var userPass = $.trim(form.find('input[name=user-pass]').val());
			if (!userName || !userPass) {
				return false;
			}
			if (isNaN(userName) || userPass.length < 6 || userName.length < 10) {
				return false;
			}
			return true;
		}

		alert = function (text) {
			if ($('#alert-modal').find('.am-modal-bd').html()) {
				$('#alert-modal').find('.am-modal-bd').html(text);
				$('#alert-modal').modal();
			} else {
		    	$('body').append(template.alert(text)) ;
		    	$('#alert-modal').modal();
		    }
		}

		confirm = function (text, onConfirm) {
			if ($('#confirm-modal').find('.am-modal-bd').html()) {
				$('#confirm-modal').find('.am-modal-bd').html(text);
			} else {
				$('body').append(template.confirm(text));
			}
			$('#confirm-modal').modal({
		        relatedTarget: this,
		        onConfirm: function (options) {
		          	location.href = '?s=/Mobile/User/userLogin';
		        },
		        onCancel: function() {
		        	console.log('sds');
		        }
		    });
		}

		return {
			alert: alert,
			confirm: confirm,
			checkUserInfo: checkUserInfo
		};

	})();


	var template = (function () {

		var alertModal, _alertModalHTML = '',
			confirmModal, _confirmModalHTML = '',
			questionPanel, _questionPanelHTML = '';


		questionPanel = function () {
			_questionPanelHTML += '<div class="am-panel am-panel-default">';
			  	_questionPanelHTML += '<div class="am-panel-hd">';
			  		_questionPanelHTML += '<span>';
				  		_questionPanelHTML += '<img class="am-circle" src="Public/mobile/images/lufei.jpg" width="30" height="30">';
				  	_questionPanelHTML += '</span>&nbsp;';
				  	_questionPanelHTML += '<span class="am-vertical-align-middle">重庆邮电大学学生会</span>';
			  	_questionPanelHTML += '</div>';
			  	_questionPanelHTML += '<div class="am-panel-bd">';
			    	_questionPanelHTML += '<p>这里是问题的详情这里是问题的详情这里是问题的详情这里是问题的详情</p>';
			  	_questionPanelHTML += '</div>';
			  	_questionPanelHTML += '<div class="am-panel-footer">';
			  		_questionPanelHTML += '<div class="post-time"><i class="am-icon-calendar"></i><span>7-12 15:30</span></div>';
			  		_questionPanelHTML += '<div class="am-fr comment">';
			  			_questionPanelHTML += '<i class="am-icon-comments"></i>';
			  			_questionPanelHTML += '<span>10</span>';
			  		_questionPanelHTML += '</div>';
			  		_questionPanelHTML += '<div class="am-fr praise">';
			  			_questionPanelHTML += '<i class="am-icon-thumbs-up"></i>';
			  			_questionPanelHTML += '<span>20</span>';
			  		_questionPanelHTML += '</div>';
			  	_questionPanelHTML += '</div>';
			_questionPanelHTML += '</div>';
			questionPanelHTML = _questionPanelHTML;
			_questionPanelHTML = '';
			return questionPanelHTML;
		}

		alertModal = function (text) {
			_alertModalHTML += '<div id="alert-modal" class="am-modal am-modal-alert" tabindex="-1">';
	 	 		_alertModalHTML += '<div class="am-modal-dialog">';
	    			_alertModalHTML += '<div class="am-modal-hd">友情提示</div>';
	    				_alertModalHTML += '<div class="am-modal-bd">';
	      					_alertModalHTML += text;
	    				_alertModalHTML += '</div>';
	    			_alertModalHTML += '<div class="am-modal-footer">'
	      		_alertModalHTML += '<span class="am-modal-btn">确定</span>';
	    	_alertModalHTML += '</div></div></div>';
	    	return _alertModalHTML;
		}

		confirmModal = function (text) {
			_confirmModalHTML += '<div class="am-modal am-modal-confirm" tabindex="-1" id="confirm-modal">';
			  	_confirmModalHTML += '<div class="am-modal-dialog">';
				    _confirmModalHTML += '<div class="am-modal-hd">友情提示</div>';
				    	_confirmModalHTML += '<div class="am-modal-bd">';
				      		_confirmModalHTML += text;
				    		_confirmModalHTML += '</div>';
				    	_confirmModalHTML += '<div class="am-modal-footer">';
				    _confirmModalHTML += '<span class="am-modal-btn" data-am-modal-confirm>戳我登录</span>';
			    _confirmModalHTML += '<span class="am-modal-btn" data-am-modal-cancel>我就看看</span>';
			_confirmModalHTML += '</div></div></div>';
			return _confirmModalHTML;
		}

		return {
			alert: alertModal,
			confirm: confirmModal,
			question: questionPanel
		};
	})();