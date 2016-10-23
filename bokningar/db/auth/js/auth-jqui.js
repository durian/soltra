
function show_error(msg) {
  $(document.createElement('div'))
      .attr({title: 'Alert', 'class': 'alert'})
      .html(msg)
      .dialog({
          buttons: {OK: function(){$(this).dialog('close');}},
          close: function(){$(this).remove();},
          draggable: true,
          modal: true,
          resizable: false,
          width: 'auto'
    });   
}
function show_info(msg) {
  $(document.createElement('div'))
      .attr({title: 'Alert', 'class': 'alert'})
      .html(msg)
      .dialog({
          buttons: {OK: function(){$(this).dialog('close');}},
          close: function(){$(this).remove();},
          draggable: true,
          modal: true,
          resizable: false,
          width: 'auto'
    });   
}
function show_sticky(msg) {
  $(document.createElement('div'))
      .attr({title: 'Alert', 'class': 'alert'})
      .html(msg)
      .dialog({
          buttons: {OK: function(){$(this).dialog('close');}},
          close: function(){$(this).remove();},
          draggable: true,
          modal: true,
          resizable: false,
          width: 'auto'
    });    
}

