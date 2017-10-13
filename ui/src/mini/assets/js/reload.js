const body = document.body,
  devaation = 50
let touchArea, tip, touchStartOffset, touchMoveOffset, scrollBarOffset;

const scrollRefresh = function () {
  touchArea = document.getElementById('scrollTouch')
  tip = document.getElementById('scroll-tip')
  // 下拉偏移值 ，触发回调

  console.log('init')
  /**
   * flag有3种状态
   * 0 = 初始化状态
   * 1 = 滚动中
   * 2 = 执行相应的回调函数
   */
  var flag = 0,
    timer1, timer2;


  // 设置偏移量
  function translate(num) {
    touchArea.style.transform = 'translateY(' + num + 'px)'
  }

  // 触摸开始，初始化偏移值


  function scrollTouchStart(e) {
    let c = touchArea.className;
    if (flag != 2) {
        touchArea.className =''

    }

    var target = e.targetTouches[0];
    scrollBarOffset = body.scrollTop;
    touchStartOffset = target.clientY;
  }

  // 移动
  function scrollTouchMove(e) {
    var target = e.targetTouches[0];
    touchMoveOffset = target.clientY;
    // 判断滚动条到顶部 并 处于下拉状态

    if (body.scrollTop < 1 && (touchMoveOffset > touchStartOffset)) {
      e.preventDefault();
      if (flag != 2) {
        var num = (touchMoveOffset - touchStartOffset - scrollBarOffset) * 0.6;

        num = num > 100 ? 100 : num;


        if (num < 80) {
          tip.innerHTML = '下拉刷新...';
          flag = 0;
        } else {
          tip.innerHTML = '释放刷新...';
          flag = 1;
        }
        translate(num);
      }

      // alert(2)

    }
  }

  // 结束
  function scrollTouchEnd(e) {
    if (flag == 1) {
      flag = 2;
      tip.innerHTML = '正在加载...';
      touchArea.className = 'onload'
      translate(80);

      if (timer1) {
        clearTimeout(timer1)
      }

      timer1 = setTimeout(function () {
        touchArea.className = ''
        addList();
      }, 500)

    } else if (flag == 2) {

      return false;
    } else {
        touchArea.className = 'onload'
        setTimeout(function() {
            touchArea.className = ''
        }, 1000);
      translate(0);
      flag = 0;
    }
  }


  touchArea.addEventListener('touchstart', scrollTouchStart, false);
  touchArea.addEventListener('touchmove', scrollTouchMove, false);
  touchArea.addEventListener('touchend', scrollTouchEnd, false);

  // 更新列表操作
  function addList() {
    // tip.off('touchstart');
    touchArea.className = 'onload2'
    translate(0);
    if (timer2) {
      clearTimeout(timer2)
    }
    tip.innerHTML = '加载完成';

    timer2 = setTimeout(function () {
      flag = 0;
      touchArea.className =""
    }, 1300)

  }

}

export default scrollRefresh;
