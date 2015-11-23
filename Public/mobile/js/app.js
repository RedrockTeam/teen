

	var user = (function () {

		var login;

		login = function () {
            if (verify.userLoginVerify('#user-login-form')) {
				var $button = $(this);
	            $.ajax({
	            	url: 'index.php?s=/Home/Index/login',
	            	type: 'POST',
	            	dataType: 'json',
	            	data: $('#user-login-form').serialize(),
	            	beforeSend: function () {
	            		$button.button('loading');
	            	}
	            }).done(function (response, status) {
	            	if (response.status == 200 && status === 'success') {
	            		$.AMUI.utils.cookie.set('username', response.username);
	            		location.href = '?s=/Mobile';
	            	} else {
	            		view.alert("你的账号或密码有误");
	            	}
	            }).fail(function (jqXHR, textStatus) {
	            	view.alert('稍安勿躁, 好像出了点小问题=_=');
	            }).always(function() {
	            	setTimeout(function () {
	            		$button.button('reset');
	            	}, 2000);
	            });
            } else {
            	view.alert('用户名或密码输入有误~');
            }
		}
		return {
			login: login
		};
	})();


	var question = (function () {

		var add,
			praise;

		addViewShow = function () {
			view.addQuestionViewShow();
		} 

		add = function () {
			// view.addQuestionViewHide();
			// return;
			var $button = $(this);
			var result = verify.questionAddVerify('#question-form');
			if (result) {
				$.ajax({
	            	url: 'index.php?s=/Home/Index/commit_voice',
	            	type: 'POST',
	            	dataType: 'json',
	            	data: result,
	            	beforeSend: function () {
	            		$button.button('loading');
	            	}
	            }).done(function (response, status) {
	            	if (response.status === 200) {
	            		view.alert('提问成功~', function () {
	            			response.data.time = tool.formatTime(response.data.time);
							// 隐藏提问页面
							view.addQuestionViewHide();
							// 动态添加最新问题
							view.addQuestionPanel(response.data);
						});
	            	} else {
	            		view.alert("问题数据不完整, 请重新");
	            	}
	            }).fail(function (jqXHR, textStatus) {
	            	view.alert('稍安勿躁, 好像出了点小问题=_=');
	            }).always(function() {
	            	$button.button('reset');
	            	$('form')[0].reset();
	            });
			}
		};

		praise = function () {
			
		};

		commentViewShow = function () {
			$('#question-comment-modal').modal({
				width: $(window).width() * 0.8
			});
		};

		comment = function () {
			if (verify.questionCommentVerify($('#comment').val())) {
				var data = {
					id: $('input[name=voiceId]').val(),
					comment: $('#comment').val()
				}
				$.ajax({
					url: 'index.php?s=/Home/Index/commit_comment',
					type: 'POST',
					data: data
				}).done(function (response, status) {
					if (response.status == 200 && status === 'success') {
						view.alert('评论成功', function () {
							// 关闭评论Modal
							$('#question-comment-modal').modal('close');
							// 添加最新评论
							view.addQuestionCommentLi(response.data);
							// 评论数字加一
							view.questionCommentInc();
						})
					} else {
						view.alert('评论失败');
					}
				}).fail(function() {
					view.alert('系统出了点小问题, 刷新试试');
				}).always(function() {
					// 情况评论表单
					$('form')[0].reset();
					// 关闭评论Modal
					$('#question-comment-modal').modal('close');
				});
				
			}
		}

		return {
			add: add,
			praise: praise,
			comment: comment,
			addViewShow: addViewShow,
			commentViewShow: commentViewShow
		};
	})();

	// 验证模块
	var verify = (function () {
		var userLoginVerify,
			questionAddVerify,
			questionCommentVerify;

		// 用户登录验证
		userLoginVerify = function (form) {
			var userName = $.trim($(form).find('input[name=username]').val());
			var userPass = $.trim($(form).find('input[name=password]').val());
			if (!userName || !userPass) {
				return false;
			}
			if (isNaN(userName) || userPass.length < 6 || userName.length < 10) {
				return false;
			}
			return true;
		};

		// 提问验证
		questionAddVerify = function (form) {
			var flag = true;
			var formData = $(form).serialize();
			var tempData = tool.parseArgs(formData);
			for (var name in tempData) {
				if (!$.trim(tempData[name])) {
					flag = false;
					$('#' + name).addClass('am-form-field');
					$('#' + name).parent().addClass('am-form-error');
				} else {
					continue;
				}
			}
			return flag == true ? formData : flag;
		};

		// 问题评论验证
		questionCommentVerify = function (value) {
			if (!$.trim(value)) {
				$('#comment').addClass('am-form-field');
				$('#comment').parent().addClass('am-form-error');
				return false;
			}
			return true;
		};

		return {
			userLoginVerify: userLoginVerify,
			questionAddVerify: questionAddVerify,
			questionCommentVerify: questionCommentVerify
		}
	})();


	var view = (function () {

		var alert, 
			confirm, 
			addQuestionPanel, 
			addQuestionViewShow,
			addQuestionCommentLi;

		alert = function (text, callback) {
			if ($('#alert-modal').find('.am-modal-bd').html()) {
				$('#alert-modal').find('.am-modal-bd').html(text);
				$('#alert-modal').modal({
		    		relatedTarget: this,
					onConfirm: callback
				});
			} else {
		    	$('body').append(template.alert(text));
		    	$('#alert-modal').modal({
		    		relatedTarget: this,
					onConfirm: callback
				});
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

		// 提问页面显示
		addQuestionViewShow = function () {
			$('.add-question').removeClass('closeChooseListAnimation').addClass('openChooseListAnimation').css('-webkit-transform', 'scale(0)');
			setTimeout(function () {
				$('#index').css('display', 'none');
				$('#question').removeClass('bounceOut').addClass('fadeIn animated').css('display', 'block');
			}, 400);
		};

		// 提问页面隐藏
		addQuestionViewHide = function () {
			$('#question').removeClass('fadeIn').addClass('bounceOut animated');
			$('#index').css('display', 'block');
			$('#question').css('display', 'none');
			$('.add-question').removeClass('openChooseListAnimation').addClass('closeChooseListAnimation').css('-webkit-transform', 'scale(1)');
		};

		// 
		addQuestionPanel = function (data) {
			var $questionPanel = $(template.question(data)).addClass('animated pulse');
			$('.am-g:eq(1)').prepend($questionPanel);
			setTimeout(function () {
				$questionPanel.removeClass('animated pulse');
			}, 1000);
		};

		// 动态添加问题评论
		addQuestionCommentLi = function (data) {
			var $questionCommentLi = $(template.comment(data)).addClass('animated pulse');
			$('.am-comments-list').prepend($questionCommentLi);
			setTimeout(function () {
				$questionCommentLi.removeClass('animated pulse');
			}, 1000);
		}

		// 问题评论数增1
		questionCommentInc = function () {
			var count = $('#user-comments-list span:eq(0) b').text();
			$('#user-comments-list span:eq(0) b').text(++count);
		}

		return {
			alert: alert,
			confirm: confirm,
			addQuestionPanel: addQuestionPanel,
			questionCommentInc: questionCommentInc,
			addQuestionViewShow: addQuestionViewShow,
			addQuestionViewHide: addQuestionViewHide,
			addQuestionCommentLi: addQuestionCommentLi
		};

	})();


	var tool = (function () {
		var parseArgs,
			formatTime;
			
		parseArgs = function (formData) {
			var args = {};
			var pairs = formData.split("&"); 
			for(var i = 0; i < pairs.length; i++) {
	            var index = pairs[i].indexOf('=');
	            if (index == -1) {
	            	continue;
	            } else {
	                var name = pairs[i].substring(0, index);
	                var value = decodeURIComponent(pairs[i].substring(index+1));
	                args[name] = value;
	            }
	        }
	        return args;
		}

		formatTime = function (timestamp) {
			var time = new Date(timestamp * 1000);
			var month = time.getMonth() + 1;
			var day = time.getDate();
			var hour = time.getHours();
			var min = time.getMinutes();
			return ' ' + month + '-' + day + ' ' + hour + ':' + min;
		};

		return {
			parseArgs: parseArgs,
			formatTime: formatTime
		};
	})();


	var template = (function () {
		var commentLi, _commentLiHTML = '',
			alertModal, _alertModalHTML = '',
			confirmModal, _confirmModalHTML = '',
			questionPanel, _questionPanelHTML = '';
		
		commentLi = function (data) {
			_commentLiHTML += '<li class="am-comment">';
                _commentLiHTML += '<article>';
                    _commentLiHTML += '<a href="javascript:void(0)">';
                        _commentLiHTML += '<img src="'+ data.face +'" class="am-comment-avatar" width="48" height="48"/>';
                    _commentLiHTML += '</a>';
                    _commentLiHTML += '<div class="am-comment-main">';
                        _commentLiHTML += '<header class="am-comment-hd">';
                            _commentLiHTML += '<div class="am-comment-meta">';
                                _commentLiHTML += '<a href="javascript:void(0)" class="am-comment-author">'+ data.username +'&nbsp;</a>评论于';
                                _commentLiHTML += '<time>'+ data.time +'</time>';
                            _commentLiHTML += '</div>';
                        _commentLiHTML += '</header>';
                        _commentLiHTML += '<div class="am-comment-bd">'+ data.comment +'</div>'
                    _commentLiHTML += '</div>';
                _commentLiHTML += '</article>';
            _commentLiHTML += '</li>';
            commentLiHTML = _commentLiHTML;
            _commentLiHTML = '';
            return commentLiHTML;
		}

		questionPanel = function (data) {
			_questionPanelHTML += '<div class="am-panel am-panel-default">';
			  	_questionPanelHTML += '<div class="am-panel-hd">';
			  		_questionPanelHTML += '<span>';
				  		_questionPanelHTML += '<img class="am-circle" src="Public/mobile/images/lufei.jpg" width="30" height="30">';
				  	_questionPanelHTML += '</span>&nbsp;';
				  	_questionPanelHTML += '<span class="am-vertical-align-middle">' + data.postername + '</span>';
			  	_questionPanelHTML += '</div>';
			  	_questionPanelHTML += '<div class="am-panel-bd">';
			    	_questionPanelHTML += '<p>' + data.question + '</p>';
			  	_questionPanelHTML += '</div>';
			  	_questionPanelHTML += '<div class="am-panel-footer">';
			  		_questionPanelHTML += '<div class="post-time"><i class="am-icon-calendar"></i><span>'+ data.time +'</span></div>';
			  		_questionPanelHTML += '<div class="am-fr comment">';
			  			_questionPanelHTML += '<i class="am-icon-comments"></i>';
			  			_questionPanelHTML += '<span>0</span>';
			  		_questionPanelHTML += '</div>';
			  		_questionPanelHTML += '<div class="am-fr praise">';
			  			_questionPanelHTML += '<i class="am-icon-thumbs-up"></i>';
			  			_questionPanelHTML += '<span>0</span>';
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
	    			_alertModalHTML += '<div class="am-modal-hd">温馨提示</div>';
	    				_alertModalHTML += '<div class="am-modal-bd">';
	      					_alertModalHTML += text;
	    				_alertModalHTML += '</div>';
	    			_alertModalHTML += '<div class="am-modal-footer">'
	      		_alertModalHTML += '<span class="am-modal-btn" data-am-modal-confirm>确定</span>';
	    	_alertModalHTML += '</div></div></div>';
	    	return _alertModalHTML;
		}

		confirmModal = function (text) {
			_confirmModalHTML += '<div class="am-modal am-modal-confirm" tabindex="-1" id="confirm-modal">';
			  	_confirmModalHTML += '<div class="am-modal-dialog">';
				    _confirmModalHTML += '<div class="am-modal-hd">温馨提示</div>';
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
			comment: commentLi,
			confirm: confirmModal,
			question: questionPanel
		};
	})();