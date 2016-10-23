var stack_topleft = {"dir1": "down", "dir2": "right", "push": "top"};

function show_error(msg) {
  $.pnotify({
    title: 'Error',
    text: msg,
    styling: 'jqueryui',
    type: 'error',
    addclass: "stack-topleft",
    stack: stack_topleft,
    history: false
  });    
}
function show_info(msg) {
  $.pnotify({
    title: 'Info',
    text: msg,
    styling: 'jqueryui',
    type: 'info',
    addclass: "stack-topleft",
    stack: stack_topleft,
    history: false
  });    
}
function show_sticky(msg) {
  $.pnotify({
    title: 'Info',
    text: msg,
    styling: 'jqueryui',
    type: 'info',
    addclass: "stack-topleft",
    stack: stack_topleft,
    history: false,
    hide: false
  });    
}

