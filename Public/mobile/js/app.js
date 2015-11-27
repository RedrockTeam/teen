
	
	(function init () {
		// 个人中心
		$('.am-icon-user').on('click', function () {
			if (!$.AMUI.utils.cookie.get('stunum')) {
				view.confirm('你还未登陆');
			} else {
				location.href = $('html').attr('data-personal');	
			}
		});

		// 主页Icon
		$('.am-icon-home').on('click', function () {
			location.href = $('html').attr('data-index');
		});
	})()

	var user = (function () {

		var login;

		login = function () {
            if (verify.userLoginVerify('#user-login-form')) {
				var $button = $(this);
	            $.ajax({
	            	url: $('html').attr('data-login'),
	            	type: 'POST',
	            	data: $('#user-login-form').serialize(),
	            	beforeSend: function () {
	            		$button.button('loading');
	            	}
	            }).done(function (response, status) {
	            	if (response.status == 200 && status === 'success') {
	            		location.href = $('html').attr('data-index');
	            	} else {
	            		view.alert("你的账号或密码有误");
	            	}
	            }).fail(function (XMLHttpRequest, status) {
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

		var postMap = {
			add: $('html').attr('data-add'),
			praise: $('html').attr('data-praise'),
			comment: $('html').attr('data-comment')
		};


		var add,
			praise;

		addViewShow = function () {
			if ($.AMUI.utils.cookie.get('stunum')) {
				view.addQuestionViewShow();
			} else {
				view.confirm('提问需要先登录!');
			}
		};

		add = function () {
			var $button = $(this);
			var result = verify.questionAddVerify('#question-form');
			if (result) {
				$.ajax({
	            	url: postMap.add,
	            	type: 'POST',
	            	dataType: 'json',
	            	data: result,
	            	beforeSend: function () {
	            		$button.button('loading');
	            	}
	            }).done(function (response, status) {
	            	if (response.status === 200) {
	            		var data = response.data;
	            		data.time = tool.formatTime(data.time);
	            		view.alert('提问成功~', function () {
							// 隐藏提问页面
							view.addQuestionViewHide();
							// 动态添加最新问题
							view.addQuestionLi(data);
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
			var data = {id: $('input[name=voiceId]').val()};
			$.post(postMap.praise, data, function(response, textStatus) {
				if (response.status == 200) {
					view.alert('点赞成功', function () {
						// 点赞动画
						view.praiseQuestion();
						// 点赞数增一
						view.questionPraiseInc();
					});
				} else if (response.status == 304) {
					view.alert('不能重复点赞');
				} else {
					view.alert('稍安勿躁, 好像出了点小问题=_=');
				}
			}).error(function () {
				view.alert('稍安勿躁, 好像出了点小问题=_=');
			});
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
					url: postMap.comment,
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
					// 清空评论表单
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
			addQuestionLi, 
			addQuestionViewShow,
			addQuestionCommentLi;

		// 重写Alert方法
		alert = function (text, callback) {
			if ($('#alert-modal').find('.am-modal-bd').html()) {
				$('#alert-modal').find('.am-modal-bd').html(text);
			} else {
		    	$('body').append(template.alert(text));
		    }
		    if (!callback) {
		    	callback = function () {
		    		return;
		    	}
		    }
	    	$('#alert-modal').modal({
	    		relatedTarget: this,
				onConfirm: callback
			});
		}

		// 重写Confirm方法
		confirm = function (text, onConfirm) {
			if ($('#confirm-modal').find('.am-modal-bd').html()) {
				$('#confirm-modal').find('.am-modal-bd').html(text);
			} else {
				$('body').append(template.confirm(text));
			}
			$('#confirm-modal').modal({
		        relatedTarget: this,
		        onConfirm: function (options) {
		          	location.href = $('html').attr('data-login');
		        }
		    });
		};

		// 提问页面显示
		addQuestionViewShow = function () {
			$('.add-question').removeClass('closeChooseListAnimation').addClass('openChooseListAnimation').css({
				'-webkit-transform': 'scale(0)'
			});
			// FAB按钮的动画是300ms, 为了炫一点, 在动画完成之后再显示, 你可以选择打我T_T
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
			$('.add-question').removeClass('openChooseListAnimation').addClass('closeChooseListAnimation').css({
				'-webkit-transform': 'scale(1)'
			});
		};

		// 动态添加问题
		addQuestionLi = function (data) {
			var $questionLi = $(template.question(data)).addClass('animated pulse');
			$('.am-list-news ul').prepend($questionLi);
			setTimeout(function () {
				$questionLi.removeClass('animated pulse');
			}, 1000);
		};

		// 动态添加问题评论
		addQuestionCommentLi = function (data) {
			if ($('.sad-face')) {
				$('.am-comments-list').css('display', 'block');
				$('.sad-face').remove();
			}
			var $questionCommentLi = $(template.comment(data)).addClass('animated pulse');
			$('.am-comments-list').prepend($questionCommentLi);
			setTimeout(function () {
				$questionCommentLi.removeClass('animated pulse');
			}, 1000);
		};

		// 问题评论数增1
		questionCommentInc = function () {
			var count = $('#user-comments-list span:eq(0) b').text();
			$('#user-comments-list span:eq(0) b').text(++count);
		};

		// 点赞数增1
		questionPraiseInc = function () {
			var count = $('#user-comments-list span:eq(1) b').text();
			$('#user-comments-list span:eq(1) b').text(++count);
		};

		// 点赞动画
		praiseQuestion = function () {
			$('.question-praise-div i').addClass('praiseAnimate');
			setTimeout(function () {
				$('.question-praise-div span').text('已赞');
				$('.question-praise-div i').css('color', '#dd514c').removeClass('praiseAnimate am-icon-thumbs-o-up').addClass('am-icon-thumbs-up');
			}, 1005);
		};

		return {
			alert: alert,
			confirm: confirm,
			addQuestionLi: addQuestionLi,
			praiseQuestion: praiseQuestion,
			questionPraiseInc: questionPraiseInc,
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
			questionLi, _questionLiHTML = '',
			alertModal, _alertModalHTML = '',
			confirmModal, _confirmModalHTML = '';
		
		commentLi = function (data) {
			_commentLiHTML += '<li class="am-comment">';
                _commentLiHTML += '<article>';
                    _commentLiHTML += '<a href="javascript:void(0)">';
                        _commentLiHTML += '<img src="'+ data.touxiang +'" class="am-comment-avatar" width="48" height="48"/>';
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

		questionLi = function (data) {
			_questionLiHTML += '<li class="am-g am-list-item-desced">';
	          	_questionLiHTML += '<a href="'+ $('html').attr('data-detail').substr(0, $('html').attr('data-detail').length - 5) + '/id/' + data.id + '">' + data.title + '</a>';
	          	_questionLiHTML += '<span class="am-list-date">发表日期: '+ data.time +'</span>';
	          	_questionLiHTML += '<div class="am-list-item-text">'+ data.question +'</div>';
	      	_questionLiHTML += '</li>';
			_tempQuestionLiHTML = _questionLiHTML;
			_questionLiHTML = '';
			return _tempQuestionLiHTML;
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
			question: questionLi,
			confirm: confirmModal
		};
	})();