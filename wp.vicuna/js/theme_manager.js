(function($){
	var ct = 'index', page = 0, searchQuery, layouts = ['index', 'category', 'archive', 'tag', 'page', 'single', 'search', '404'];
	if(typeof themes.length === 'number') themes = {};
	var sampleChanger = function(){
		var $t = $(this);
		$t.pleaseWait(true);
		$.post('admin-ajax.php?action=vicuna_sample_url', {
			cs: $('#vicuna-skin').val(),
			ce: $('#vicuna-eye_catch').val(),
			cg_d: $('input:checked[name=vicuna-g_navi-display]').val(),
			cg_p: $('input:checked[name=vicuna-g_navi-pos]').val(),
			cg_h: $('input:checked[name=vicuna-g_navi-home]').val(),
			cd: $('input:checked[name=vicuna-description]').val(),
			cn: $('input:checked[name=vicuna-nocenter]').val(),
			cf: $('#vicuna-fixed_width').val(),
			ct: ct,
			cl_i: $('#vicuna-layout-index').val(),
			cl_e: $('#vicuna-layout-single').val(),
			cl_p: $('#vicuna-layout-page').val(),
			cl_a: $('#vicuna-layout-archive').val(),
			cl_c: $('#vicuna-layout-category').val(),
			cl_t: $('#vicuna-layout-tag').val(),
			cl_n: $('#vicuna-layout-404').val(),
			cl_s: $('#vicuna-layout-search').val(),
			cc_e: $('input:checked[name=vicuna-enable_color]').val(),
			cc_t: $('#vicuna-color').val()
		}, function(res){
			if (res) $('iframe#vicunaThemeSampler').contents()[0].location.href = res;
			$t.pleaseWait();
		});
	}
	var colorChanger = function(){
		var t = themes[$(this).attr('id')];
		$('#vicuna-color').val('themeid*'+t.themeid+',title*'+t.title+',hex*'+t.hex.join('|'));
		$('#currentColor').html($('#'+t.themeid).clone().removeClass('hover')).find('ul')
			.hover(function(){$(this).addClass('hover')}, function(){$(this).removeClass('hover')})
			.click(function(){$('#vicunaColorEditor').fadeIn(500);});
		$('#vicunaColorEditor .colorTheme ul').remove();
		$('#vicunaColorEditor .colorTheme').append($('#currentColor ul').clone()).find('li')
			.hover(function(){$(this).addClass('hover')}, function(){$(this).removeClass('hover')});
		$('#vicunaColorEditor input.title').val(t.title);
		$('#vicunaColorEditor .colorTheme li').each(registerColorEditor);
		sampleChanger();
	}
	var rgb2hex = function(rgb){
		rgb = rgb.match(/[0-9]{1,3}/g);
		rgb = (Number(rgb[2]) + 256 * Number(rgb[1]) + 65536 * Number(rgb[0])).toString(16);
		while(rgb.length < 6) rgb = '0'+rgb;
		return rgb;
	}
	var registerColorEditor = function(){
		var $t = $(this);
		$t.ColorPicker({
			color: '#'+rgb2hex(this.style.backgroundColor),
			onShow: function (colpkr) {
				$t.addClass('pick');
				$(colpkr).fadeIn(500);
				return false;
			},
			onHide: function (colpkr) {
				$t.removeClass('pick').css('backgroundColor', '#' + $(colpkr).find('div.colorpicker_hex input').val());
				$(colpkr).fadeOut(500);
				return false;
			}
		});
	}

	$('#vicunaThemeSampler').iframe({
		width: '100%'
	});
	$.extend($.prototype, {
		pleaseWait: function(settings){
			if (settings === true) {
				var $f = $(this).parents('form'), offset = $f.offset();
				$f.after('<div id="pleaseWait"></div><div id="pleaseWaitMessage">Please Wait...</div>');
				$('#pleaseWait').width($f.outerWidth()).height($f.outerHeight()).css('top', offset.top);
				$('#pleaseWaitMessage').css({
					left: (($f.outerWidth() - $('#pleaseWaitMessage').width()) / 2) + offset.left,
					top: (($f.outerHeight() - $('#pleaseWaitMessage').height()) / 2) + offset.top
				});
			}
			else {
				$('#pleaseWait').remove();
				$('#pleaseWaitMessage').remove();
			}
		}
	});

	$('#vicuna-skin').change(function(){
		$('#tab3').hide();
		for (var i = 0; i < colorSkins.length; i++) {
			if ($('#vicuna-skin').val() === colorSkins[i]) {
				$('#tab3').show();
				break;
			}
		}
		if(!$('#tab3:hidden').length && $('#tab3').hasClass('current')){
			$('#tab3').removeClass('current');
			$('#tab0').addClass('current');
			$('#themeManagerForm table.form-table:visible, #themeManagerForm div.form-div:visible').hide();
			$($('#themeManagerForm table.form-table,#themeManagerForm div.form-div')[0]).show();
		}
		var sl, s = skin_data[$(this).val()];
		for (var l = 0; l < layouts.length; l++) {
			sl = $('#vicuna-layout-' + layouts[l]).val();
			$('#vicuna-layout-' + layouts[l] + ' option').remove();
			for (var i = 0; i < s.Layout.length; i++) {
				$('#vicuna-layout-' + layouts[l]).append('<option value="' + s.Layout[i] + '">' + s.Layout[i] + '</option>');
				if (s.Layout[i] === sl) $('#vicuna-layout-' + layouts[l]).val(sl);
			}
		}
		sl = $('#vicuna-eye_catch').val();
		$('#vicuna-eye_catch option').remove();
		for (var i = 0; i < s.Eye_catch.length; i++) {
			$('#vicuna-eye_catch').append('<option value="' + s.Eye_catch[i] + '">' + s.Eye_catch[i] + '</option>');
			if (s.Eye_catch[i] === sl) $('#vicuna-eye_catch').val(sl);
		}
		sl = $('#vicuna-fixed_width').val();
		$('#vicuna-fixed_width option').remove();
		for (var i = 0; i < s.Fixed_width.length; i++) {
			$('#vicuna-fixed_width').append('<option value="' + s.Fixed_width[i] + '">' + s.Fixed_width[i] + '</option>');
			if (s.Fixed_width[i] === sl) $('#vicuna-fixed_width').val(sl);
		}
		sampleChanger();
	});
	$('#sampleChanger a').click(function(){
		$('#sampleChanger a').removeClass('current');
		ct = $(this).attr('href');
		sampleChanger();
		$(this).addClass('current');
		return false;
	});
	$('#vicuna-eye_catch,#vicuna-layout-index,#vicuna-layout-category,#vicuna-layout-archive,#vicuna-layout-tag,#vicuna-layout-page,#vicuna-layout-single,#vicuna-layout-search,#vicuna-layout-404,#vicuna-fixed_width').change(sampleChanger);
	$('input[name^=vicuna-g_navi],input[name=vicuna-description],input[name=vicuna-nocenter]').click(sampleChanger);
	$('#tabs1 a').click(function(){
		$('#themeManagerForm table.form-table:visible, #themeManagerForm div.form-div:visible').hide();
		$($('#themeManagerForm table.form-table,#themeManagerForm div.form-div')[$(this).attr('id').match(/[0-9]+/)[0]]).show();
		$('#tabs1 a.current').removeClass('current');
		$(this).addClass('current');
		return false;
	});
	$($('#themeManagerForm table.form-table,#themeManagerForm div.form-div')[$('#tabs1 a.current').attr('id').match(/[0-9]+/)[0]]).show();

	$('#vicunaColorSearchButton').click(function(){
		if(!$('#vicunaColorSearchText').val()) return false;
		searchQuery = $('#vicunaColorSearchText').val();
		var sv = $(this).val();
		$(this).addClass('loading').val(waitMessage).attr('disable', true);
		$.post('admin-ajax.php?action=vicuna_color', {a:'search',q:searchQuery}, function(res){
			res = eval('('+res+')');
			if(!res.success){
				$('#vicunaColorSearchButton').removeClass('loading').val(sv).attr('disable', false);
				return false;
			}
			$('#vicunaColorSearchResults>li').remove();
			var $kul = $('#vicunaColorSearchResults');
			for (var i = 0; i < res.items.length; i++) {
				var $ul = $('<ul></ul>');
				for(var ii = 0; ii < res.items[i].hex.length; ii++){
					$ul.append('<li style="background:#'+res.items[i].hex[ii]+';"></li>');
				}
				$kul.append('<li><ul title="'+res.items[i].title+'" id="'+res.items[i].themeid+'" class="colorTheme">'+$ul.html()+'</ul></li>');
				themes[res.items[i].themeid] = res.items[i];
			}
			page = res.page;
			if(res.page < 1) $('#vicunaColorSearchPaging span.prev').addClass('disable');
			else $('#vicunaColorSearchPaging span.prev').removeClass('disable');
			if(res.page >= Math.round(res.count / 10)) $('#vicunaColorSearchPaging span.next').addClass('disable');
			else $('#vicunaColorSearchPaging span.next').removeClass('disable');
			$('#vicunaColorSearchResults ul')
				.hover(function(){$(this).addClass('hover')}, function(){$(this).removeClass('hover')})
				.click(colorChanger);
			$('#vicunaColorSearchButton').removeClass('loading').val(sv).attr('disable', false);
		});
	});
	$('#vicunaColorSearchPaging span')
		.hover(function(){$(this).addClass('hover')}, function(){$(this).removeClass('hover')})
		.click(function(){
			if($(this).hasClass('disable')) return false;
			var sv = $('#vicunaColorSearchButton').val();
			$('#vicunaColorSearchButton').addClass('loading').val(waitMessage).attr('disable', true);
			$('#vicunaColorSearchPaging span').addClass('disable');
			if($(this).hasClass('prev')) page--;
			else if($(this).hasClass('next')) page++;
			$.post('admin-ajax.php?action=vicuna_color', {a:'search',q:searchQuery, p:page}, function(res){
				res = eval('('+res+')');
				if(!res.success){
					$('#vicunaColorSearchButton').removeClass('loading').val(sv).attr('disable', false);
					return false;
				}
				$('#vicunaColorSearchResults li').remove();
				var $kul = $('#vicunaColorSearchResults');
				for (var i = 0; i < res.items.length; i++) {
					var $ul = $('<ul></ul>');
					for(var ii = 0; ii < res.items[i].hex.length; ii++){
						$ul.append('<li style="background:#'+res.items[i].hex[ii]+';"></li>');
					}
					$kul.append('<li><ul title="'+res.items[i].title+'" id="t'+res.items[i].themeid+'">'+$ul.html()+'</ul></li>');
					themes[res.items[i].themeid] = res.items[i];
				}
				page = res.page;
				$('#vicunaColorSearchResult ul').click(colorChanger);
				if(res.page < 1) $('#vicunaColorSearchPaging span.prev').addClass('disable');
				else $('#vicunaColorSearchPaging span.prev').removeClass('disable');
				if(res.page >= Math.round(res.count / 10)) $('#vicunaColorSearchPaging span.next').addClass('disable');
				else $('#vicunaColorSearchPaging span.next').removeClass('disable');
				$('#vicunaColorSearchResults ul').hover(function(){$(this).addClass('hover')}, function(){$(this).removeClass('hover')});
				$('#vicunaColorSearchButton').removeClass('loading').val(sv).attr('disable', false);
			});
		});
	$('input[name=vicuna-enable_color]').click(function(){
		if($(this).val() === '0') $('div.form-div table:eq(1)').hide();
		else $('div.form-div table:eq(1)').show();
		sampleChanger();
	});
	if($('input:checked[name=vicuna-enable_color]').val() === '0') $('div.form-div table:eq(1)').hide();
	if($('#vicuna-color').val()){
		var t = {}, c = $('#vicuna-color').val().split(',');
		for(var i = 0; i < c.length; i++){
			c[i] = c[i].split('*');
			if(c[i][0] === 'hex') c[i][1] = c[i][1].split('|');
			t[c[i][0]] = c[i][1];
		}
		var $ul = $('<ul id="t'+t.themeid+'" title="'+t.title+'"></ul>');
		for(i = 0; i < t.hex.length; i++)
			$ul.append('<li style="background:#'+t.hex[i]+';"></li>');
		$('#currentColor').append($ul);
		$('#currentColor ul')
			.hover(function(){$(this).addClass('hover')}, function(){$(this).removeClass('hover')})
			.click(function(){$('#vicunaColorEditor').fadeIn(500);});
		$('#vicunaColorEditor .colorTheme').append($ul.clone()).find('li')
			.hover(function(){$(this).addClass('hover')}, function(){$(this).removeClass('hover')});
		$('#vicunaColorEditor input.title').val(t.title);
		$('#vicunaColorEditor .colorTheme li').each(registerColorEditor);
	}

	$('#vicunaColorEditor input.done').click(function(){
		$('#currentColor').html($('#vicunaColorEditor div.colorTheme ul').clone());
		var $ul = $('#currentColor ul');
		$ul.hover(function(){$(this).addClass('hover')}, function(){$(this).removeClass('hover')})
			.click(function(){$('#vicunaColorEditor').fadeIn(500);})
			.attr('title', $('#vicunaColorEditor input.title').val())
			.attr('id', 'm0');
		themes[$ul.attr('id')] = {themeid: $ul.attr('id'), title: $ul.attr('title'), hex: [
			rgb2hex($ul.find('li:eq(0)').css('background-color')),
			rgb2hex($ul.find('li:eq(1)').css('background-color')),
			rgb2hex($ul.find('li:eq(2)').css('background-color')),
			rgb2hex($ul.find('li:eq(3)').css('background-color')),
			rgb2hex($ul.find('li:eq(4)').css('background-color'))
		]}
		var t = themes[$ul.attr('id')];
		$('#vicuna-color').val('themeid*'+t.themeid+',title*'+t.title+',hex*'+t.hex.join('|'));
		sampleChanger();
		$('#vicunaColorEditor').fadeOut(500);
	});
	$('#vicunaColorEditor input.reset').click(function(){
		var $ul = $('#currentColor ul').length? $('#currentColor ul').clone():
			$('<ul><li style="background:#FFF;"></li><li style="background:#FFF;"></li><li style="background:#FFF;"></li><li style="background:#FFF;"></li><li style="background:#FFF;"></li></ul>');
		$('#vicunaColorEditor div.colorTheme ul').remove();
		$('#vicunaColorEditor div.colorTheme').append($ul);
		$('#vicunaColorEditor div.colorTheme li').each(registerColorEditor);
	});
	$('#tab3').hide();
	for (var i = 0; i < colorSkins.length; i++) {
		if ($('#vicuna-skin').val() === colorSkins[i]) {
			$('#tab3').show();
			break;
		}
	}
	if($('#tab3:hidden').length && $('#tab3').hasClass('current')){
		$('#tab3').removeClass('current');
		$('#tab0').addClass('current');
		$('#themeManagerForm table.form-table:visible, #themeManagerForm div.form-div:visible').hide();
		$($('#themeManagerForm table.form-table,#themeManagerForm div.form-div')[0]).show();
	}
	var $ul, $li;
	for(i in themes){
		$ul = $('<ul title="'+themes[i].title+'" id="'+themes[i].themeid+'" class="colorTheme"></ul>');
		for(var ii = 0; ii < themes[i].hex.length; ii++){
			$ul.append('<li style="background:#'+themes[i].hex[ii]+';"></li>');
		}
		$li = $('<li></li>')
			.append('<span class="deleteButton">X</li>')
			.append($ul);
		$('#colorThemes').append($li);
	}
	$('#colorThemes ul')
		.hover(function(){$(this).addClass('hover')}, function(){$(this).removeClass('hover')})
		.click(colorChanger);
	$('#colorThemes span.deleteButton')
		.hover(function(){$(this).addClass('hover').next().addClass('delHover')}, function(){$(this).removeClass('hover').next().removeClass('delHover')})
		.click(function(){
			var $t = $(this);
			$t.removeClass('hover').addClass('loading');
			$.post('admin-ajax.php?action=vicuna_delete_color', {themeid:$(this).next().attr('id')}, function(res){
				if(res == '1') $t.parent().remove();
				else $t.removeClass('loading');
			});
		});

	$(document).ready(function(){
		$.get('admin-ajax.php?action=vicuna_color', null, function(res){
			res = eval('('+res+')');
			if(!res.success){
				return false;
			}
			$('#recentUploads li,#popularColors li').remove();
			for (var i = 0; i < res.items.popular.length; i++) {
				var $ul = $('<ul title="'+res.items.popular[i].title+'" id="'+res.items.popular[i].themeid+'" class="colorTheme"></ul>');
				for(var ii = 0; ii < res.items.popular[i].hex.length; ii++){
					$ul.append('<li style="background:#'+res.items.popular[i].hex[ii]+';"></li>');
				}
				$('#popularColors').append($('<li></li>').append($ul));
				themes[res.items.popular[i].themeid] = res.items.popular[i];
			}
			for (var i = 0; i < res.items.recent.length; i++) {
				var $ul = $('<ul title="'+res.items.recent[i].title+'" id="'+res.items.recent[i].themeid+'" class="colorTheme"></ul>');
				for(var ii = 0; ii < res.items.recent[i].hex.length; ii++){
					$ul.append('<li style="background:#'+res.items.recent[i].hex[ii]+';"></li>');
				}
				$('#recentUploads').append($('<li></li>').append($ul));
				themes[res.items.recent[i].themeid] = res.items.recent[i];
			}
			$('ul.colorTheme')
				.hover(function(){$(this).addClass('hover')}, function(){$(this).removeClass('hover')})
				.click(colorChanger);
		});
		sampleChanger();
	});
})(jQuery);
