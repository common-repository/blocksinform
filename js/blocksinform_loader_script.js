window._blocksinform = window._blocksinform || [];
_blocksinform.push({placement_type:'auto'});


!function (e, f, u) {
	e.type = 'hidden';
  e.id = 'blocks_inform_id';
  e.value = u;
  f.parentNode.insertBefore(e, f);
}(document.createElement('input'), document.getElementsByTagName('meta')[0], pub_vars.pub_id);

!function (e, f, u) {
	e.rel = 'stylesheet';
  e.href = u;
  f.parentNode.insertBefore(e, f);
}(document.createElement('link'), document.getElementsByTagName('script')[0], '//reg.blocksinform.com/css/wp_'+ pub_vars.pub_id+'.css');

!function (e, f, u) {
	e.async = 1;
  e.src = u;
  f.parentNode.insertBefore(e, f);
}(document.createElement('script'), document.getElementsByTagName('script')[0], '//reg.blocksinform.com/js/wp_loader_'+ pub_vars.pub_id+'.js');