const directives = {
  drop: {
    x: 0,
    y: 0,
    canMove: false,
    inserted(el, binding) {
      let that = binding.def;
      let offsetX = 0;

      el.addEventListener('mousedown', function (e) {
        if (e.target && e.target.id == 'move_head' || e.target.nodeName == "B" && e.target.offsetParent.id == 'move_head') {
          if (e.target.id == 'move_head') {
            offsetX = el.offsetWidth - e.target.offsetWidth;
          }
          if (e.target.nodeName == "B" && e.target.offsetParent.id == 'move_head') {
            offsetX = el.offsetWidth - e.target.offsetParent.offsetWidth;
          }
          that.x = e.offsetX;
          that.y = e.offsetY;
          el.style.top = el.offsetTop + 'px';
          el.style.left = el.offsetLeft + 'px';
          that.canMove = true;
          document.addEventListener('mousemove', _move, false)
        }

      }, false)

      function _move(e) {

        if (that.canMove) {
          el.style.top = (e.clientY - that.y) + 'px';
          el.style.left = (e.clientX - that.x - offsetX) + 'px';
        }
      }

      document.addEventListener('mouseup', function (e) {
        that.canMove = false;
        document.removeEventListener('mousemove', _move)
      }, false)

    },
  },

  autoPosition: {
    inserted(el, binding) {
      let that = binding.def;
      let className = el.className;
      console.log(className)
      let _array = document.getElementsByClassName('reply-min-box'),
        top = 0;
      if (_array.length > 0) {
        let _h = _array.length > 1 ? 36 : 32;
        top = _h * (_array.length - 1);

      }
      el.style.top = top + 'px'

    }

  },
  pull: { //下拉加载
    inserted(el, binding, vnode) {
      let arg = binding.arg || 0;
      arg = parseInt(arg,10);
      let cb = binding.expression;
      let t = null;

      el.addEventListener('scroll', function (e) {
        let target = e.target;
        let scrollTop = target.scrollTop;

        clearTimeout(t);
        t = setTimeout(function () {

          if (scrollTop <= arg) {
            if (vnode.context[cb]) {
                vnode.context[cb].call()
            }

          }
        }, 200)
      }, false)
    }

  }
}
export default directives
