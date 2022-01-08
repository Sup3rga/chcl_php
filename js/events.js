$(function(){
    Loader.animate(".panel-load");
    $('body')
    .on('focus','.field .input', function(){
        var parent = $(this).parent();
        parent = parent.hasClass('combined') ? parent.parent() : parent;
        parent.addClass('animated').removeClass('invalid');
    }) //@component
    .on('blur', '.field .input', function(){
        if($(this).val().length == 0){
            var parent = $(this).parent();
            parent = parent.hasClass('combined') ? parent.parent() : parent;
            parent.removeClass('animated');
        }
    }) //@component
    .on('change', '.field .input', function(){
        var parent = $(this).parent();
        parent = parent.hasClass('combined') ? parent.parent() : parent;
        parent[$(this).val().length ? 'addClass': 'removeClass']('animated');
    })
    .on('click', '.combined .password-toggler', function(){
        var cls = $(this).attr('alternate').split(","),
            $this = $(this),
            input = $(this).parent().find('.input');
        input.attr('type', $this.hasClass(cls[0]) ? 'text' : 'password');
        $this.toggleClass(cls[0]).toggleClass(cls[1]);
    }) //@component
    .on('click', '#login', function(){
        var btn = $(this);
        if(btn.attr('locked') != undefined){
            return;
        }
        var data = Form.serialize($('#logger'));
        if(Form.isSubmitable(data)){
            AlertBox.set('#logger .alert-box')
                .text("Requête en cours...")
                .icon("ion-load-d")
                .type("")
                .show();
            btn.attr('locked', true);
            User.signin(data)
            .then(function(r){
                btn.removeAttr('locked');
                AlertBox.text(r.message)
                .type(r.code == 1 ? "success" : "warn")
                .icon(r.code == 1 ? "ion-android-done" : "ion-android-warning")
            }).catch(function(e){
                btn.removeAttr('locked');
                AlertBox.text(e)
                    .icon("ion-alert")
                    .type("fail")
            });
        }
        else{
            AlertBox.set('#logger .alert-box')
                .type('fail')
                .text("Veuillez tout remplir correctement !")
                .icon("ion-compose")
                .show();
        }
    })
    .on('click', 'sidemenu .item', function (){
        Modules.updateSideMenuUi($(this));
    })
    .on('click', '#user-panel-toggler', function(){
        if($(this).hasClass('ion-chevron-down')){
            $(this).removeClass('ion-chevron-down').addClass('ion-chevron-up');
            $('.user-panel').show();
        }
        else{
            $(this).addClass('ion-chevron-down').removeClass('ion-chevron-up');
            $('.user-panel').hide();
        }
    })
    .on('click', '#logout', function(){
        $('#user-panel-toggler').trigger('click');
        DialogBox.setMessage("<icon class='ion-alert rounded yellow'></icon>Vous allez vous déconnecter")
        .setType('YESNO')
        .show()
        .onClose(function(e,r){
            if(r){
                User.signout().catch(function(e){
                    AlertBox.text(e)
                        .icon("ion-alert")
                        .type("fail")
                });
            }
        });
    })
    .on('click', '.action .combined button', function(){
        if($(this).parent().hasClass('automatic')) return;
        var lastText = set($(this).parent().attr('last-choice'), "").split(',');
        $(this).parent().removeClass('invalid');
        if(lastText.length == 1 && lastText[0].length == 0){
            lastText = [];
        }
        $(this).parent().find('button').each(function(){
            if(!$(this).hasClass('off')){
                lastText.push($(this).attr('data-value'));
                return false;
            }
        });
        $(this).parent().find('button').addClass('off');
        $(this).removeClass('off');
        $(this).parent().val($(this).attr('data-value'));
        if($(this).attr('data-value') != lastText){
            $(this).parent().trigger('change').attr('last-choice', lastText.join(','));
        }
    }) //@component
    .on('rollback', '.action .combined', function(){
        var last = $(this).attr('last-choice').toString().split(),
            $this = $(this),
            set = false;
        if(last.length == 1 && last[0] == "undefined"){
            last = [];
        }
        $(this).find('button').each(function(){
            if($(this).attr('data-value') == last[last.length - 1] && !set){
                $(this).removeClass('off');
                last.pop();
                $this.attr('last-choice', last.join(','));
                set = true;
            }
            else{
                $(this).addClass('off');
            }
        });
    }) //@component <-
    .on('click', '.register-n-list-switch', function(){
        var btn = $(this),
            found = false,
            parent = btn.parent();
        while(!parent.hasClass('register-n-list')){
            if(parent[0].tagName.toLowerCase() == 'body'){
                found = false;
                break;
            }
            found = true;
            parent = parent.parent();
        }

        if(found){
            parent.toggleClass('fugitif');
            btn.toggleClass('rotate');
            var t = setTimeout(function(){
                clearTimeout(t);
                // btn[!parent.hasClass('fugitif') ? 'addClass' : 'removeClass']('la-plus')
                // [parent.hasClass('fugitif') ? 'addClass' : 'removeClass']('la-step-backward');
            },200);
        }
    }) //@component
    .on('click', 'sidemenu .company-logo', function(){
        $('sidemenu').toggleClass('minimal');
    })
    .on('click', '.form-popup-caller', function(){
        FormPopup.title('show').show();
    })
    .on('input', 'appbar .search-bar', function(){
        var reg = $(this).val().replace(/(\.|\+|\?|\*|\\|\|\^|\$)/g, '\\($1)');
        Modules.search($(this).val(),reg);
    })
    .on('input', '.input[mask]', function(){
        var mask = $(this).attr('mask'),
            val = $(this).val(),
            val_mask = '';
        for(var i in mask){
            if(val[i] == undefined) break;
            while(['#','X'].indexOf(mask[i]) < 0){
                val_mask += mask[i];
                if(i < mask.length - 1){
                    if(val[i] != mask[i]){
                        val_mask += val[i];
                        i++;
                    }
                    else{
                        break;
                    }
                }
                else{
                    break;
                }
            }
            if(i >= mask.length) break;
            if(mask[i] == '#'){
                if(/[0-9]/.test(val[i])) val_mask += val[i];
            }
            else if(mask[i] == "X"){
                if(/[^0-9]/.test(val[i])) val_mask += val[i];
            }
        }
        $(this).val(val_mask);
    })
    .on('focus', '.input[match]', function(){
        var parent = $(this).parent();
        while(!parent.hasClass('field') && parent[0].tagName.toLowerCase() != 'body'){
            parent = parent.parent();
        }
        if(parent.hasClass('field')){
            parent.removeClass('invalid')
        }
    })
    .on('blur', '.input[match]', function(){
        var reg = $(this).attr('match'),
            parent = $(this).parent();
        if(!new RegExp(reg,"i").test($(this).val())){
            $(this).val('');
            $(this).trigger('change');
            while(!parent.hasClass('field') && parent[0].tagName.toLowerCase() != 'body'){
                parent = parent.parent();
            }
            if(parent.hasClass('field')){
               parent.addClass('invalid')
            }
        }
    })
    .on('click', '.list-item .summary', function(){
        $(this).parent().toggleClass('open');
    })
    .on('click', '.action .toggler', function(){
        $(this).toggleClass('on').val($(this).hasClass('on')).trigger('change');
    }) //@Component
    .on('rollback', '.action .toggler', function(){
        $(this).toggleClass('on').val($(this).hasClass('on'))
    }) //@Component
    //call
    $('.field .input').trigger('change');
});