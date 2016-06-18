$(document).ready(function(){
	$('.add-project-btn').click(function(){
		$('#projectModal').modal('toggle')
	})

	$(document).on('click', '.user-project-btn', function(){
		window.location = base_url + 'api?project_id=' + $(this).attr('alt')
	})

	$('#modal_ok_btn').click(function(){
		var project_name = $('#projectNameInput').val()

		$.ajax({
			url: base_url + 'project/create',
			type: 'POST',
			data: { 'project_name': project_name },
			success: function(data){
				$('#content-list-wrap').append(
					'<div class="content-item user-project-btn" alt="' + data +'">' +
						'<div class="item-body">' +
							'<p>' + project_name + '</p>' +
							'<div class="line"></div>' +
							'<img src="' + assets_url + '/img/icon/local_p.png' +'" alt="">' +
						'</div>' +
						'<div class="item-footer">' +
							'<p>Modify</p>' +
						'</div>' +
					'</div>'
				)

				$('#projectModal').modal('hide')
			}
		})
	})
})