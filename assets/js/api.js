$(document).ready(function(){
	$('body').css({'height' : $(window).height() - 80})

	$('#side-button-area > ul > li').click(function(){
		var _this = $(this)

		if ($(this).attr('id') == 'test-view-btn'){
			window.open(project_url + 'docs', "_blank")
		}
		else {
			_this.parent().find('li').removeClass('active')
			_this.addClass('active')

			if ($(this).attr('id') == 'db-view-btn'){
				$('#db-wrap').css({'display':'block'})
				$('#side-item-area').css({'display':'none'})
				$('#edit-wrap').css({'display':'none'})
				$('#draggable-item-list').css({'display':'none'})
				$.ajax({
					url: base_url + 'api/db_list?project_id=' + current_project_id,
					type: 'GET',
					success: function(data){
						$('#db-list-area').empty()
						$.each(data, function(index, value) {
						    var column_item = 
						    	'<div class="db-item">' +
						    		'<div class="db-header">' +
						    			index +
						    		'</div>' +
						    		'<div class="column-area">' +
						    			'<div class="user-header column-header">' +
						    				'<img src="http://localhost/~a1/doing/assets/img/icon/circle.png" alt="">' +
											'<span>Column</span>' +
										'</div>' +
										'<div class="column-body">'
							$.each(value, function(index, column){
								column_item = column_item +
									'<div class="column-item">' +
										column +
									'</div>'
							})
							column_item = column_item + '</div></div></div>'
							$('#db-list-area').append(column_item)
						}); 
					}
				})
			}
			else{
				$('#db-wrap').css({'display':'none'})
				$('#side-item-area').css({'display':'block'})
				$('#edit-wrap').css({'display':'block'})
				$('#draggable-item-list').css({'display':'block'})
			}
		}
	})

	$('.down').click(function(){
		var item_list = $(this).parents('.item-header').siblings('.item-list')
		item_list.css({ 'display' : item_list.css('display') == 'none' ? 'block' : 'none'})
	})

	$('#url').click(function(){
		window.open($(this).text(), "_blank")
	})

	$('#edit-import-btn').click(function(){
		$('input[type=file]').click()
	})

	var html_name = null
	var html_code = null
	$('#edit-import-area > input').change(function(){
		$('#edit-api-nav').empty()

		var fReader = new FileReader();
		fReader.readAsDataURL(this.files[0]);
		html_name = this.files[0].name.split('.')[0]
		fReader.onloadend = function(event){
			$.ajax({
				url: event.target.result,
				type: 'GET',
				success: function(data){
					html_code = data
				}
			})
			$('#edit-user-area > iframe').attr('src', event.target.result)
			$('#edit-user-area').css({'display': 'block'})
			$('#url > p').text(project_url + html_name)
		}
	})

	var item_list_position = $('#component > .item-list').position()
	$('#draggable-item-list').css({
		'left' : item_list_position.left,
		'top' : item_list_position.top
	})

	var drag_flag = false
	var mouse_position
	$( document ).on( "mousemove", function( event ) {
		mouse_position = {
			x: event.pageX,
			y: event.pageY
		}

		if ($('#edit-user-area').css('display') == 'block' && 
			mouse_position.x >= 330 && mouse_position.y >= 80 && drag_flag){
			var edit_cover = $('#edit-cover')
			if (edit_cover.css('display') == 'none'){
				edit_cover.css('display', 'block')
				edit_cover.animate({
					opacity: 0.8
				}, 300)
			}
		}
	})

	var api_btn_color = {
		'login': 'default',
		'join': 'warning',
		'search': 'primary',
		'mail': 'success',
		'chat': 'info',
		'cart': 'danger',
	}
	$('#draggable-item-list > ul > li').draggable({
		start: function(){
			drag_flag = true
		}
	})

	$('#draggable-item-list > ul > li').mouseup(function(){
		var _this = $(this)
		drag_flag = false
		$('#edit-cover').animate({
			opacity: 0
		}, 300, function(){
			$(this).css('display', 'none')
		})
		if ($('#edit-user-area').css('display') != "none" && mouse_position.x >= 330 && mouse_position.y >= 80){
			_this.animate({
				opacity : 0,
			}, 100, function(){
				_this.css({'left':0, 'top':0, 'opacity':1})
			})

			var api_btn_name = _this.attr('id').split('-btn')[0]
			$('#edit-api-nav').css({'display':'block'})
			$('#edit-api-nav').append('<span class="label label-' + api_btn_color[api_btn_name] + '">' +
				api_btn_name +
			'</span>')
		}
		else{
			_this.animate({
				left: 0,
				top: 0,
			}, 300)
		}
	})

	$('#save-btn').click(function(){
		$.ajax({
			url: base_url + 'api/create',
			type: 'POST',
			data: {
				project_id: current_project_id,
				page_name: html_name,
				html_content: html_code,
				success_callback: $('#callback-wrap > .user-content > textarea').val(),
				api_kind: $('#edit-api-nav > span').eq(0).text()
			},
			success: function(data){
				console.log(data)
				alert("Save Success")
			},
			fail: function(){
				alert("Save fail")
			}
		})
	})

	var api_option_map = {
		'login' : ['callback'],
		'join' : ['callback'],
		'mail' : ['callback']
	}
	$(document).on('click', '#edit-api-nav > span', function(){
		$('#option').css({'display':'block'})
		$('#option > .item-list > .item-wrap').css({'display':'none'})

		api_option_map[$(this).text()].forEach(function(val, index){
			$('#' + val + '-wrap').css({'display': 'block'})
		})
	})
})



