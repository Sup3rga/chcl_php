var conform = {
        toFunction: function(e){
            if(/function\(([a-z0-9, ]*?)\)\{([\s\S]*)\}$/.test(e)){
                var args, fn = {
                    args: RegExp.$1,
                    body: RegExp.$2
                }
                args = fn.args.split(',');
                var arg = [];
                foreach(args, function(i){
                    arg.push(i.replace(' ', ''))
                })
                return new Function(arg, fn.body);
            }
            else
                return function(){};
        },
        sortFunction:function(a,b){
            if(a < b)
                return 1;
            else
                return -1;
        },
        toFormalName: function(e){
            var c = false, r = '', e = (e+"").toString().toLowerCase(), k=0;
            for(var i in e){
                if(!k)
                    c = true;
                r += c ? e[i].toUpperCase() : e[i];
                if(inArray(e[i], [' ', '-', '\'']))
                    c = true;
                else
                    c = false;
                k++;
            }
            return r;
        },
        toCamelCase: function(text,pascal){
            var pascal = set(pascal,false), c = false,
                avoid = [' ', '-', '_', '\''],
                r = '', e = text.toLowerCase();
            foreach(e,function(i,j){
                r += !inArray(i,avoid) ? ( c || (j == 0 && pascal) ? i.toUpperCase() : i ) : '';
                if(inArray(i,avoid))
                    c = true;
                else
                    c = false;
            })
            return r;
        },
        toBoolean: function(e){
            return !/false|0|null/i.test(e);
        },
        sortIndex: function(e){
            var e = set(e,{}), t = [], r = {};
            foreach(e, function(i){
                t.push(i);
            })
            t.sort(this.sortFunction);
            foreach(t, function(i){
                foreach(e, function(j,k){
                    if(j == i)
                        r[k] = j;
                })
            })
            return r;
        },
        sortObject: function(e){
            var t = objectIndex(e).sort(), r={};
            foreach(t, function(i,j){
                r[i] = e[i];
            })
            return r;
        }
    },
    detect = {
        isNumber: function(e){
            return /^[0-9]+(\.[0-9]+)?$/.test(e);
        },
        isInt: function(e){
            return /^[0-9]+$/.test(e);
        },
        isEmail: function(e){
            return /^[a-z0-9._-]+@[a-z0-9._-]+\.[a-z0-9]{2,6}$/i.test(e);
        },
        isName: function(e){
            return /^[a-záäąàãâȧéëēèêęėíïįıìĩîóöǫȯòõúüūųùýỹçċñǹ '-]+$/i.test(e);
        },
        isNameCode: function(e){
            return /^[a-záäąàãâȧéëēèêęėíïįıìĩîóöǫȯòõúüūųùýỹçċñǹ@&.'-]+/i.test(e);
        },
        isString: function(e){
            return /^[a-z]/i.test(e) && /[\S\s]+$/i.test(e);
        },
        isFunction: function(e){
            return typeof e == 'function' ||
                ( typeof e == 'string' && /^function([a-z_0-9]+)?\(([a-z0-9, ]*?)\)\{([\s\S]*)\}$/.test(e));
        },
        password: {
            isWeak: function(e){
                var e = e.toString();
                return e.length <= 6 &&
                    (/^[a-z]{1,6}$/i.test(e) || /[^a-z0-9]+/i.test(e) || /^[0-9]{4,6}$/.test(e) ||
                        /^[a-z0-9]{1,6}$/i.test(e));
            },
            isMedium: function(e){
                var e = e.toString();
                return e.length > 6 && e.length < 10 &&
                    (/^[a-z]{6,10}$/i.test(e) || /[^a-z0-9]+/i.test(e) || /^[0-9]{7,10}$/.test(e) ||
                        /^[a-z0-9]{6,10}$/i.test(e));
            },
            isStrong: function(e){
                var e = e.toString();
                return e.length > 8 &&
                    (/^[a-z]{8,}$/i.test(e) || /[^a-z0-9]+/i.test(e) || /^[0-9]{8,}$/.test(e) ||
                        /^[a-z0-9]{8,}$/i.test(e));
            },
            is: function(e){
                var t = ['Weak', 'Medium', 'Strong'], r = '';
                for(var i in t){
                    if(detect.password['is'+t[i]](e)){
                        r = t[i].toLowerCase();
                        break;
                    }
                }
                return r;
            }
        },
        dateComparison(date, date2){
            var d1 = date.split('-'),
                d2 = date2.split('-');
            if(d1.length != 3 || d2.length != 3) return 0;
            var r = 0;
            for(var i in d1){
                if(d1[i] * 1 < d2[i] * 1){
                    r = -1;
                }
                if(d1[i] * 1 > d2[i] * 1){
                    r = 1;
                }
                if(r != 0) break;
            }
            return r;
        }
    };

function inArray(item, array, igcase){
    var rep = false,
        igcase = typeof igcase == 'bool' || igcase == 0 || igcase == 1 ? igcase : false;
    for(var i = 0, j = array.length; i < j; i++){
        if(igcase){
            array[i].toLowerCase(); item = item.toLowerCase();
        }
        if(item == array[i]){
            rep = true;
            break;
        }
    }
    return rep;
}

function isJSON(m){
    if (typeof m == 'object' && m != null) {
        try{ m = JSON.stringify(m); }
        catch(err) { return false; } }

    if (typeof m == 'string') {
        try{ m = JSON.parse(m); }
        catch (err) { return false; } }

    if (typeof m != 'object' || m == null) { return false; }
    return true;

}

function empty(str){
    var rep = true, state = 0;
    if(typeof str != 'object' || str == null){
        if(!((!/^null$/.test(str) && !/undefined/.test(typeof str) && str.toString().length == 0) || /^null$/.test(str)))
            rep = false;
    }
    else{
        for(var i in str){
            rep = empty(str[i]);
            if(!rep)
                state++;
        }
        if(state)
            rep = false;
    }
    return rep
}

function len(data){
    if(data == null) return 0;
    var k = 0;
    for(var i in data){
        k++;
    }
    return k;
}

function removeElement(t, e){
    var n = [];
    for(var i in t){
        if(t[i] != e){
            n.push(t[i]);
        }
    }
    return n;
}

function removeIndex(t,i){
    var n = [];
    for(var k in t){
        if(k != i){
            n.push(t[k]);
        }
    }
    return n;
}

function isset(str){
    return typeof str != 'undefined' && str != null;
}

function set(e, v, s){
    return isset(s) ? (e ? isset(v) ? v : null : isset(s) ? s : null) : (isset(e) ? e : isset(v) ? v : null);
}

function extend(model, options, ref){
    var ref = set(ref, false),
        e = ref ? model : JSON.parse(JSON.stringify(model)),
        r = e;
    for(var i in options){
        r[i] = options[i];
    }
    return r;
}

function merge(a1, a2, r){
    var rep = [],
        r = isset(r) ? r : false;
    for(var i in a1){
        rep.push(a1[i]);
    }
    for(var i = 0, j = a2.length; i < j; i++){
        if(!r || (r && !inArray(a2[i], rep)))
            rep.push(a2[i])
    }
    return rep;
}

function sigma(e){
    e = e.split(/(?::)/)
    var r = 0;
    for(var i in e){
        r += parseInt(e[i]) * (i == 0 ? 60 : 1);
    }
    return r;
}

localforage.config({
    driver      : localforage.INDEXEDDB,
    name        : 'Akademy',
    version     : 1.0,
    storeName   : 'AkademyDataBase',
    description : 'for storage of Akademy client-side'
});
localforage.ready();

var Form = {
    serialize : function(form){
        var data = {}, input;
        form.find('[name]').each(function(){
            input = $(this);
            data[input.attr('name')] = input.attr('type') == 'checkbox' ? this.checked : input.val();
        })
        return data;
    },
    reset: function(form, data){
      data = set(data,{});
      var input, name;
      form.find('[name]').each(function(){
          input = $(this);
          name = input.attr('name');
          if(name in data){
              if(input.attr('type') == 'checkbox'){
                  this.checked = data[name];
              }
              else{
                  input.val(data[name]);
              }
          }
          else{
              if(input.attr('type') == 'checkbox'){
                  this.checked = false;
              }
              else{
                  input.val('');
              }
          }
          data[input.attr('name')] = input.attr('type') == 'checkbox' ? this.checked : input.val();
      })
    },
    isSubmitable : function(data){
        var r = true;
        for(var i in data){
            if(data[i].toString().length == 0){
                r = false;
                break;
            }
        }
        return r;
    },
    lock: function(el){
        $(el).attr('locked', "true");
        return this;
    },
    unlock: function(el){
        $(el).removeAttr('locked');
        return this;
    },
    isLocked: function(el){
        return $(el).attr('locked') == "true";
    }
},
Fetch = {
    get: function(url, data){
        return new Promise(function(resolve,reject){
            $.get(url, data, function(e){
                if(isJSON(e)){
                    var r = JSON.parse(e);
                    if('data' in r){
                        Modules.updateData(r.data);
                    }
                    if(r.error){
                        reject(r.message);
                    }
                    else{
                        resolve(r);
                    }
                    User.readCode(r.code);
                }
                else{
                    reject("Une erreur est survénue lors du traitement");
                }
            }, function(){
                reject("Une erreur est survénue lors du traitement");
            });
        });
    },
    post: function(url,data){
        return new Promise(function(res, rej){
            $.post(url, data, function(e){
                if(isJSON(e)){
                    var r = JSON.parse(e);
                    if('data' in r){
                        Modules.updateData(r.data);
                    }
                    if(r.error){
                        rej(r.message);
                    }
                    else{
                        res(r);
                    }
                    User.readCode(r.code);
                }
                else{
                    rej("Une erreur est survénue lors du traitement");
                }
            }, function(){
                rej("Une erreur est survénue lors du traitement");
            });
        });
    }
}
AlertBox = {
    hidden : true,
    box : null,
    set : function(selector){
        this.box = $(selector);
        return this;
    },
    text : function(text){
        this.box.find('.text').html(text);
        return this;
    },
    icon : function(cls){
        this.box.find('icon').attr('class', cls);
        return this;
    },
    show: function(){
        this.box.show(200);
        this.hidden = false;
        return this;
    },
    hide: function(){
        this.box.hide(200);
        this.hidden = true;
        return this;
    },
    type: function(type){
        this.box.removeClass('warn').removeClass('success').removeClass('fail')
          .addClass(type);
      return this;
    },
    toggle: function(){
        this.box[this.hidden ? 'show' : 'hide'](200);
        this.hidden = !this.hidden;
        return this;
    }
},
Loader = {
    dom: null,
    animate: function(selector){
        var loader = $(selector),
            bulbes = loader.find('.bulbe');
        loader.removeClass("stop");
        function t(n){
            bulbes.eq(n - 1 < 0 ? bulbes.length - 1 : n - 1).removeClass("up");
            bulbes.eq(n).addClass("up");
            var time = setTimeout(function(){
                clearTimeout(time);
                if(!loader.hasClass('stop')){
                    t( ( n + 1 ) % bulbes.length)
                }else{
                    bulbes.removeClass("up");
                }
            },300);
        }
        t(0);
    },
    close : function(selector){
        var loader = $(selector),
            bulbes = loader.find('.bulbe');
        loader.addClass("stop");
        bulbes.removeClass("up");
    }
},
DialogBox = {
    ev: function(){
        var box = $('.dialog-box');
        box.find('.action button').off('click');
        box.find('.action button').on('click', function(){
            DialogBox.hide();
        });
    },
    setMessage : function(html){
        this.ev();
        var box = $('.dialog-box');
        box.find('.message').html(html);
        return this;
    },
    set: function(message, type){
        return this.setMessage(message).setType(type);
    },
    setWith: function(icon,message,type){
        return this.set('<icon class="'+icon+' rounded"></icon> <span class="super super-l10">'+message+'</span>', type);
    },
    show: function(){
        this.ev();
        var box = $('.dialog-box');
        box.show(400);
        return this;
    },
    onClose: function(fn){
        var box = $('.dialog-box');
        box.find('.action button').on('click',function(){
            fn(this, $(this).hasClass('ok') || $(this).hasClass('yes'))
        });
        return this;
    },
    hide : function(){
        var box = $('.dialog-box');
        box.hide(400);
        return this;
    },
    setType: function(type){
        this.ev();
        var box = $('.dialog-box');
        switch (type.toLowerCase()){
            case 'yesno':
                box.find('.yes, .no').show();
                box.find('.ok').hide();
                break;
            case 'none':
                box.find('.yes, .no, .ok').hide();
                break;
            default:
                box.find('.yes, .no').hide();
                box.find('.ok').show();
                break;
        }
        return this;
    }
},
Modules = {
  data : {},
  res : {
      sidemenu: {
          "/" : {value: 0, content: "", icon: "las la-home", text: "Dashboard"},
          "/academic-year" : {value : 1, content : "", icon: "las la-calendar-alt", text: "Année académique"},
          "/student" : {value : 2, content : "", icon : "las la-user-graduate", text : "Étudiants"},
          "/course" : {value : 3, content : "", icon: "las la-pencil-ruler", text: "Cours"},
          "/notes" : {value : 7, content : "", icon: "las la-sticky-note", text: "Notes et bulletins"},
          "/teacher" : {value : 4, content : "",icon: "las la-chalkboard-teacher", text: "Professeurs"},
          "/admin" : {value : 5, content : "", icon : "las la-university", text: "Administration"},
          "/users" : {value: 6, content : "", icon: "las la-users-cog", text: "Utilisateurs"}
      }
  },
  access : null,
  currentOptions : null,
  search: function(){},
  setSearchVisible: function(visible){
      $('appbar .search-zone .field')[!set(visible, true) ? 'addClass' : 'removeClass']('not-super');
  },
  updateData : function(r){
    Modules.data = extend(Modules.data, r);
    if(typeof Boot != 'undefined') {
        Modules.data = extend(Modules.data, Boot.terminal.getUserById(Modules.data.id));
    }
    localforage.setItem("akademy-data", JSON.stringify(Modules.data));
  },
  searchTrigger: function(){
    $('appbar .search-bar').trigger('input');
  },
  setSideMenu : function(access){
    var html = "",
        access = access.split(","),
        res = this.res.sidemenu;
    for(var i in res){
        if(access.indexOf(res[i].value.toString()) != -1){
            html += "<a href='#"+i+"' class=\"super item super-l12 super-flex-center\" access='"+res[i].value+"'>\n" +
                "     <icon class=\""+res[i].icon+"\"></icon>\n" +
                "     <span class=\"super text\">\n" +
                            res[i].text+
                "      </span>\n" +
                "   </a>";
        }
    }
    $('sidemenu .links').html(html);
    return this.updateSideMenuUi($('sidemenu').find('.item[href="#'+History.current+'"]'));
  },
  setAppBar: function(data){
      var usr = $('.user-zone');
      usr.find('avatar').text(data.photo != null ? '' : data.prenom[0].toUpperCase())
      .css('background-image', data.photo == null ? '' : 'url(../assets/avatars/'+data.photo+')');
      usr.find('.greeting').text(data.pseudo);
      usr.find('.name').text(data.prenom+" "+data.nom);
      return this;
  },
  allowModule: function(e){
      return Modules.data.privileges.split(',').indexOf(e+"") >= 0;
  },
  hasAccessToFac: function(id){
      return id in Modules.data.attachedBranch;
  },
  hasAccessToGrade: function(grade, fac){
    var r = false;
    grade = parseInt(grade);
    if(fac != null){
        r = this.hasAccessToFac(fac);
        if(r){
            r = Modules.data.attachedBranch[fac].indexOf(grade) >= 0;
        }
    }
    else{
        for(var i in Modules.data.attachedBranch){
            if(Modules.data.attachedBranch[i].indexOf(grade) >= 0){
                r = true;
                break;
            }
        }
    }
    return r;
  },
  load: function(html, options){
      var options = options == undefined ? {} : options;
      Modules.res.sidemenu[History.current].content = html;
      options.allowModule = this.allowModule;
      options.utils = {
          len: len,
          isPartOf: this.hasAccessToFac,
          isAlsoPartOf: this.hasAccessToGrade
      }
      this.setSearchVisible();
      html = ejs.render(html, options);
      $('panel').html(html);
      if(Boot !== undefined && History.current in Boot){
          Boot[History.current]();
      }
      this.searchTrigger();
  },
  updateSideMenuUi: function(current){
      if(this.currentOptions != null){
          this.currentOptions.removeClass('active')
      }
      this.currentOptions = current;
      this.currentOptions.addClass('active');
      return this;
  }
},
History = {
    current: !window.location.hash.length ? "/" : window.location.hash.replace(/^#/, ''),
    init : function(){
        $(window).on('hashchange', function(){
            History.current = window.location.hash.replace(/^#/, '');
            History.current = History.current.length == 0 ? "/" : History.current;
            $('panel').html(Loader.dom);
            $('appbar .search-bar').val('');
            Loader.animate('.panel-loader');
            History.reach();
        });
        return this;
    },
    reach: function(){
        Modules.updateSideMenuUi($('sidemenu').find('.item[href="#'+History.current+'"]'))
        // console.log('[DATA__]',Modules.data, History.current, ">"+window.location.hash);
        if(History.current in Modules.res.sidemenu && Modules.res.sidemenu[History.current].content.length){
            Loader.close('.panel-load');
            Modules.load(Modules.res.sidemenu[History.current].content, Modules.data);
            return;
        }
        Fetch.get('./akademy', {
            res : History.current,
            akatoken: Modules.data.token
        }).then(function(r){
            Loader.close('.panel-load');
            console.log('[R]',r);
            Modules.load(r.template, Modules.data);
        }).catch(function(){  Loader.close('.panel-load'); });
        return this;
    }
},
User = {
    signin: function(data){
        return Fetch.post('./signin', data);
    },
    signout: function(){
        return Fetch.post('./signin', {
            uid : Modules.data.id,
            disconnect: Modules.data.token
        });
    },
    readCode: function(code){
        var path = window.location.pathname.replace(/^\/akademy[a-zA-Z0-9.-]*\/|\/$/gi, '');
        console.log('[Path]', path);
        switch (code){
            case 0:
                if(path != ''){
                    DialogBox.setMessage("<icon class='las la-user-lock rounded yellow'></icon> Vous n'êtes plus connecté(e) !")
                    .setType('OK')
                    .show()
                    .onClose(function(){
                        localforage.removeItem('akademy-data');
                        window.location = '../';
                    });
                }
                break;
            case 1:
                if(path != 'akademy'){
                    window.location = '../akademy';
                }
                break;
        }
    }
};
Card = function(sel){
    this.dom = $(sel);
    var $this = this,
        ev = [];
    this.dom.find('.card-tab').on('click', function(){
        var index = $(this).attr('data-index');
        $this.dom.find('.card.active, .card-tab.active').removeClass('active');
        $(this).addClass('active');
        $this.dom.find('.card[data-index="'+index+'"]').addClass('active');
        for(var i in ev){
            ev[i](index, this);
        }
    });
    this.onSwitch = function(fn){
        ev.push(fn);
    }
},
FormPopup = {
  el: null,
  ev: function(){
    FormPopup.el.find('.action button').off('click');
    FormPopup.el.on('click', '.action button', function(){
        var val = $(this).attr('data-value');
        if(val == 'cancel'){
            FormPopup.hide();
        }
    })
  },
  html: function(html){
      FormPopup.el = FormPopup.el == null ? $('.form-popup') : FormPopup.el;
      FormPopup.el.find('.body').html(html);
      return this;
  },
  title: function(title){
      FormPopup.el = FormPopup.el == null ? $('.form-popup') : FormPopup.el;
      FormPopup.el.find('.title').html(title);
      return this;
  },
  show: function(){
      if(FormPopup.el == null) return;
      FormPopup.el.addClass('active');
      this.ev();
      return this;
  },
  hide: function(){
      if(FormPopup.el == null) return;
      FormPopup.el.removeClass('active');
      return this;
  },
  onAction: function(fn){
      if(FormPopup.el == null) return;
      this.ev();
      FormPopup.el.off('click', '.action button');
      FormPopup.el.on('click', '.action button', function(){
          var submit = $(this).attr('data-value') == 'submit';
          if(!submit){
              FormPopup.hide();
          }
          fn(submit, Form.serialize(FormPopup.el.find('.body').eq(0)))
      });
      return this;
  }
};