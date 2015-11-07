// JavaScript Document
$(document).ready(function (e) {

    $('[data-animabanners]').each(function (index, element) {

        var mc = $(this);
        var itens = mc.find(mc.attr('data-animabanners'));
        var itensTotal = itens.length;
        var time = mc.attr('time') ? parseFloat(mc.attr('time')) : 5;
        var current = mc.attr('current') ? parseInt(mc.attr('current')) : 0;
        var controls = mc.attr('data-controls') ? $($(this).attr('data-controls')).children() : $('<div />');
        var interval = 0;

        itens.removeClass('hide').css({display: 'none', opacity: 0}).eq(current).css({display: 'block'});

        anima();

        mc.mouseenter(function () {
            _clearInterval();
        }).mouseleave(function () {
            _setInterval();
        });

        function anima() {

            _clearInterval();
            checkCurrent();

            if (itensTotal < 2) {
                itens.css({'display': 'block', opacity: 1});
                return null;
            }
            
            controls.removeClass('active').eq(current).addClass('active');
            
            itens.stop().animate({opacity: 0}, 'slow', function (e) {
                $(this).css('display', 'none');
                itens.eq(current).stop().css('display', 'block').stop().animate({opacity: 1}, 'slow', function (e) {
                    _setInterval();
                });
            });

            mc.trigger('update', [current]);

        }

        function prev() {
            current--;
            anima();
        }

        function next() {
            current++;
            anima();
        }

        function checkCurrent() {
            if (current >= itens.length) {
                current = 0;
            } else if (current < 0) {
                current = itens.length - 1;
            }
        }

        function _setInterval() {
            _clearInterval();
            interval = setTimeout(next, time * 1000);
        }

        function _clearInterval() {
            clearTimeout(interval);
        }

        mc.bind('next', function () {
            next();
        }).bind('prev', function () {
            prev();
        }).on('set', function (event, value) {
            current = parseInt(value);
            checkCurrent();
            anima();
        });
                
        controls.click(function(){
            mc.trigger('set', [$(this).attr('data-index')]);
        }).each(function(index){
            $(this).attr('data-index', index);
        });

    });

});