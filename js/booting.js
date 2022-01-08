var Boot = {
    terminal: {
      setAYUI: function(current){
          if(current != null){
              $('.academic .empty-info').addClass('not-super');
              $('.academic .infos').removeClass('not-super');
              $('.academic #passYear, .academic #editYear')[current.id == Modules.data.currentYear.id ? 'show' : 'hide']();
              $('.academic .switch button[index="0"]')[current.etat != 'F' ? 'addClass' : 'removeClass']('off');
              $('.academic .switch button[index="1"]')[current.etat == 'F' ? 'addClass' : 'removeClass']('off');
              $('.academic .switch button[index="1"]')[current.etat == 'F' ? 'addClass' : 'removeClass']('off');
          }
      },
      resetAYForm: function(data){
          var edit = data != undefined;
          data = set(data, {
              annee_debut : "",
              annee_fin : "",
              debut: "",
              fin: "",
              index: "-1"
          });
          $('.academic .form-add h1').html(edit ? "Année académique" : "Nouvelle Année académique");
          $('.anneeAcademique').find('.begin').text(data.annee_debut);
          $('.anneeAcademique').find('.end').text(data.annee_fin);
          $('#Aka-yearForm').find('.input.begin').val(data.debut);
          $('#Aka-yearForm').find('.input.end').val(data.fin);
          $('#Aka-yearForm')
          .find('#newYear').text(edit ? "Modifier" : "Enregistrer")
          .attr('data-index', data.index);
      },
      setFacUI: function(current){
          if(current != null) {
              $('.administration .presentation  .empty-info').addClass('not-super');
              $('.administration .presentation  .poste-infos').addClass('not-super');
              $('.administration .presentation .fac-infos').removeClass('not-super');
              $('.administration .presentation .fac-infos .nom').html(current.nom);
              $('.administration .presentation .fac-infos .academic-list').html((function(){
                  var r = "";
                  for(var i in current.stats){
                      r += "<option value='"+i+"'>"+i+"</option>";
                  }
                  return r;
              })());
              $('.administration .presentation .grade-list').html('');
              var k = 0;
              for(var i in current.grades){
                  $('.administration .presentation .grade-list').append('<div class="item super super-l12 super-flex-center">' +
              '                    <span class="super super-l4 notation">' +
                                        current.grades[i].notation+
              '                    </span>' +
              '                    <span class="super super-l4">' +
              '                        (année '+current.grades[i].niveau+')' +
              '                    </span>' +
              '                    <span class="super super-l4 action super-flex-center super-flex-align-end">' +
                      (!Modules.allowModule(35) ? "" : '<button class="edit-grade ion-edit light" data-index="'+i+'"></button>') +
                      (!Modules.allowModule(36) ? "" : '<button class="delete-grade ion-trash-b light" data-index="'+i+'"></button>') +
              '                    </span>' +
              '                </div>');
                  k++;
              }
              if(k == 0){
                  $('.administration .presentation .grade-list')
                  .html('<div class="super super-l12 empty-box super-al-center">' +
                      '      Aucun niveau enregistré !' +
                      ' </div>');
              }
          }
          else{
              $('.administration .presentation  .empty-info').removeClass('not-super');
              $('.administration .presentation  .poste-infos').addClass('not-super');
              $('.administration .presentation  .fac-infos').addClass('not-super');
              $('.administration .presentation .grade-list').html('');
          }
      },
      getGradesOf: function(fac){
        fac = parseInt(fac);
        var f = this.getFacById(fac),
            r = {};
        for(var i in f.grades){
            if(Modules.hasAccessToGrade(i, fac)){
                r[i] = f.grades[i];
            }
        }
        return r;
      },
      setPosteUI: function(current){
          if(current != null) {
              $('.administration .presentation  .empty-info').addClass('not-super');
              $('.administration .presentation .fac-infos').addClass('not-super');
              $('.administration .presentation  .poste-infos').removeClass('not-super');
          }
          else{
              $('.administration .presentation  .empty-info').removeClass('not-super');
              $('.administration .presentation  .poste-infos').addClass('not-super');
              $('.administration .presentation  .fac-infos').addClass('not-super');
          }
      },
      setUserUI: function(form,user){
        user = user == null ? {} : user;
        Form.reset(form, {
            usr_nom : set(user.nom, ''),
            usr_prenom : set(user.prenom, ''),
            usr_pseudo : set(user.pseudo, ''),
            usr_hierarchy : set(user.poste, '')
        });
        if(set(user.privileges, '').length){
            var arr = user.privileges.split(',');
            form.find('.module-box .input').each(function(){
                if($(this).attr('data-value') != 6) {
                    var isIn = arr.indexOf($(this).attr('data-value')) >= 0;
                    this.checked = isIn;
                    $(this).parent()[isIn ? 'addClass' : 'removeClass']('selected')
                    .find('.more').css('display', isIn ? 'inline-block' : 'none');
                }
            });
        }
        else{
            form.find('.module-box .input').each(function(){
                if($(this).attr('data-value') != 6) {
                    this.checked = false;
                    $(this).parent().removeClass('selected')
                    .find('.more').css('display', 'none');
                }
            })
        }
        form.find('.input').trigger('change');
        if(set(user.photo,'').length) {
            form.find('avatar').css('background-image', 'url(../assets/avatars/' + user.photo + ')').html('')
        }else{
            form.find('avatar').css('background-image', 'unset').html('<icon class="las la-user"></icon>')
        }
      },
      updateChart: function(graph, data){
        for(var i in data){
            graph.data.datasets[i].data = data[i];
        }
        graph.update();
      },
      setPostForm: function(data){
          $('.administration #posteForm [name="poste_name"]').val(data == null ? "" : data.notation);
          $('.administration #posteForm [name="poste_capacity"]').val(data == null ? "" : data.capacity);
          if(data == null){
              $('.administration #posteForm [name="poste_linked"]').val('');
          }
          else{
              $('.administration #posteForm [name="poste_linked"]').find('option[value="'+data.affectation+'"]').attr('selected', '');
          }
          $('.administration #posteForm [name="poste_value"]').val(data == null ? "" : data.value);
      },
      updateCurrentFacData: function(current){
          for(var i in Modules.data.faculty){
              if(Modules.data.faculty[i].id == current.id){
                  current = Modules.data.faculty[i];
                  break;
              }
          }
          return current;
      },
      setTeacherUI: function(form,data){
          Form.reset(form, data);
          form.find('.input').trigger('change');
          form.find('.code').text(data == undefined ? '' : data.th_code);
          form.find('h1').text(data == undefined ? 'Nouveau Professeur' : 'Professeur');
          form.find('#newTeacher').text(data == undefined ? 'Enregistrer' : 'Modifier');
      },
      setStudentUI : function(form,data){
          Form.reset(form, data);
          form.find('.input').trigger('change');
          form.find('.code').text(data == undefined ? '' : data.st_code);
          form.find('h1').text(data == undefined ? 'Nouvel Étudiant' : 'Étudiant');
          form.find('#newStudent').text(data == undefined ? 'Enregistrer' : 'Modifier');
      },
      setCourseUI : function(form,data){
          Form.reset(form, data);
          form.find('.input').trigger('change');
          form.find('h1').text(data == undefined ? 'Nouveau cours' : 'Cours');
          form.find('#newCourse').text(data == undefined ? 'Enregistrer' : 'Modifier');
      },
      courseAlreadyPromoted: function(data, academic){
          var r = false;
          for(var i in data.horaire){
              if(academic == data.horaire[i].annee_academique){
                  r = true;
                  break;
              }
          }
          return r;
      },
      getFacById: function(index){
          var r = {};
          for(var i in Modules.data.faculty){
              if(Modules.data.faculty[i].id == index){
                  r = Modules.data.faculty[i];
                  break;
              }
          }
          return r;
      },
      getTeacherById: function(index){
            var r = {};
            for(var i in Modules.data.teacher){
                if(Modules.data.teacher[i].id == index){
                    r = Modules.data.teacher[i];
                    break;
                }
            }
            return r;
      },
      getCode: function(nom, prenom){
          var code = nom[0].toUpperCase();
          var tab = prenom.split(/(?: |-)/), k = 0;
          for(var i in tab){
              if(tab[i][0] == undefined) break;
              code += tab[i][0];
              k++;
              if(k == 2) break;
          }
          code += '-';
          for(var i  = 0; i < (3 - (Modules.data.ccc+"").length); i++){
              code += '0';
          }
          code += Modules.data.ccc + 1;
          code += '-'+Modules.data.currentYear.academie.substr(Modules.data.currentYear.academie.length - 1 - (2 - k))
          return code;
      },
      displayHours: function(data){
          var container = $('#courseForm .scheduled-courses'),
              days = Modules.data.workDays;
          container.html('');
          for(var i in data){
              container.append(
                  '<item class="super super-l12 row super-flex-center" data-index="'+data[i].id+'">' +
                  '  <div class="super super-l10 hour">' +
                  '    '+days[data[i].day]+' ~ '+data[i].begin.replace(':', 'h:')+' - '+data[i].end.replace(':', 'h:')+
                  (data[i].annee_academique == Modules.data.currentYear.id ? '' : ' <span>[ '+this.getYearById(data[i].annee_academique).academie+']</span>')+
                  '  </div>' +
                  (data[i].annee_academique == Modules.data.currentYear.id ?
                      '  <icon class="super super-l2 las la-times">' +
                      '  </icon>'
                          :
                      ''
                  )+
                  '</item>'
              );
          }
      },
      getHoraire : function(filiere, niveau, annee, prof, session){
        var result = [],
            cours = Modules.data.course;
        niveau = parseInt(niveau);
        prof = parseInt(prof);
        session =  parseInt(session);

        for(var i in cours){
            if( (prof > 0 || cours[i]._filiere == filiere) &&
                ( (prof > 0 && niveau * 1 < 0) || cours[i]._niveau == niveau) &&
                this.courseAlreadyPromoted(cours[i],annee) &&
                cours[i].etat == "E" &&
                cours[i].session == session &&
                (prof < 0 || cours[i]._titulaire == prof || cours[i]._suppleant == prof )
            ){
                for(var j in cours[i].horaire) {
                    if( cours[i].horaire[j].annee_academique == annee && (
                            prof < 0 ||
                            (!cours[i].horaire[j].tp && cours[i]._titulaire == prof) ||
                            (cours[i].horaire[j].tp && cours[i]._suppleant == prof)
                        )
                    ) {
                        result.push(extend(cours[i].horaire[j], {
                            nom: cours[i].nom,
                            suppleant: cours[i].suppleant,
                            titulaire: cours[i].titulaire,
                            filiere: cours[i].niveau.filiere.nom,
                            niveau: cours[i].niveau.notation
                        }));
                    }
                }
            }
        }
        return result;
      },
      getProfesseur: function(filiere, niveau){
          var result = [], isIn = false, stop = false,
              teacher = Modules.data.teacher;
          for(var i in teacher){
              isIn = false;
              if(filiere * 1 < 0){
                  isIn = true;
              }
              else {
                  for (var j in teacher[i].filieres) {
                      if (teacher[i].filieres[j].id == filiere) {
                          if (niveau * 1 < 0) {
                              isIn = true;
                              break;
                          } else {
                              for (var k in teacher[i].cours) {
                                  if (teacher[i].cours[k]._niveau == niveau) {
                                      isIn = true;
                                      stop = true;
                                      break;
                                  }
                              }
                              if (stop) {
                                  break;
                              }
                          }
                      }
                  }
              }
              if(isIn){
                  result.push(teacher[i]);
              }
          }
          return result;
      },
      getCourse: function(filiere,niveau,session,annee){
          var result = [], cours = Modules.data.course;
          for(var i in cours){
              if(cours[i].etat == "E" && this.courseAlreadyPromoted(cours[i], annee) && session == cours[i].session &&
                  (filiere * 1 < 0 ||
                  (filiere == cours[i]._filiere &&
                    (niveau * 1 < 0 || niveau == cours[i]._niveau)
                  )
                  )
              ){
                  result.push(cours[i]);
              }
          }
          return result;
      },
      getCourseById: function(id){
        var cours = null, course = Modules.data.course;
        for(var i in course){
            if(course[i].id == id){
                cours = course[i];
                break;
            }
        }
        return cours;
      },
      getYearById: function(id){
            var year = null, years = Modules.data.academic;
            for(var i in years){
                if(years[i].id == id){
                    year = years[i];
                    break;
                }
            }
            return year;
        },
      getStudentById: function(id){
            var stud = null, students = Modules.data.student;
            for(var i in students){
                if(students[i].id == id){
                    stud = students[i];
                    break;
                }
            }
            return stud;
        },
      getUserById: function(id){
            var usr = {}, users = Modules.data.users;
            for(var i in users){
                if(users[i].id == id){
                    usr = users[i];
                    break;
                }
            }
            return usr;
      },
      getStudent: function(filiere,niveau){
          var result = [], etu = Modules.data.student;
          for(var i in etu){
              if(etu[i].etat == "A" &&
                  (filiere * 1 < 0 ||
                      (filiere == etu[i].niveau._filiere &&
                          (niveau * 1 < 0 || niveau == etu[i]._niveau)
                      )
                  )
              ){
                  result.push(etu[i]);
              }
          }
          return result;
      },
      getStudentFromNote: function(filiere,niveau,annee){
        var result = [], notes = Modules.data.notes, etu,
            ids = [], names = [], sortedResult = [];
        for(var i in notes){
            if(notes[i]._annee_academique == annee && ids.indexOf(notes[i].etudiant.id) < 0){
                etu = notes[i].etudiant;
                ids.push(etu.id);
                if((filiere * 1 < 0 ||
                        (filiere == etu.niveau._filiere &&
                            (niveau * 1 < 0 || niveau == this.getCourseById(notes[i]._cours)._niveau)
                        )
                    )
                ){
                    result.push(etu);
                    names.push(etu.nom+" "+etu.prenom);
                }
            }
        }
        names.sort();
        for(var i in names){
            for(var j in result){
                if(names[i] == result[j].nom+" "+result[j].prenom){
                    sortedResult.push(result[j]);
                }
            }
        }
        return sortedResult;
      },
      setProfOptions: function(list){
         var option = '<option value="">Tous les professeurs</option>';
         for(var i in list){
             option += '<option value="'+list[i].id+'">'+list[i].prenom+" "+list[i].nom+'</option>';
         }
         $('.schedular [name="teacher"]').html(option);
      },
      setScheduler : function(list, filter){
          var horaire = $('.schedular-box'),
              arrow = horaire.find('.arrow'),
              max = 10 * 60, sub = 8 * 60,
              row, pos, len, start;
          horaire.find('.course-range').html('');
          arrow.eq(0).find('span').text($('.sort-zone [name="academic"]').find("[value='"+filter.academic+"']").text())
          arrow.eq(1).find('span').text(filter.facname < 0 ? 'Toutes les filières/facultés' :$('.sort-zone [name="facname"]').find("[value='"+filter.facname+"']").text())
          arrow.eq(2).find('span').text(filter.grade < 0 ? 'Tous les niveaux' : $('.sort-zone [name="grade"]').find("[value='"+filter.grade+"']").text())
          arrow.eq(3).find('span').text($('.sort-zone [name="session"]').find("[value='"+filter.session+"']").text())
          arrow.eq(4).find('span').text(filter.teacher < 0 ? 'Tous les profs' : 'prof. '+$('.sort-zone [name="teacher"]').find("[value='"+filter.teacher+"']").text())
          for(var i in list){
              start = sigma(list[i].begin) - sub;
              pos = start / max * 100;
              len = (sigma(list[i].end) - sub - start) / max * 100;
              row = '<span class="super super-l12 row super-flex-center" style="top : calc('+pos+'% + .2em); height : calc('+len+'% - .2em)">'+
                        '<span class="super super-l12 super-al-center">'+
                            '<span class="super super-l12">'+(list[i].tp ? '[TP] ' : '')+list[i].nom+'</span>'+
                            '<span class="super super-l12">'+list[i].begin.replace(/^0/, '').replace(/:/, 'h:')+' - '+list[i].end.replace(/^0/, '').replace(/:/, 'h:')+'</span>'+
                            (filter.teacher > 0 ? '' : '<span class="super super-l12">'+
                                (list[i].tp ? (list[i].suppleant == null ? 'N/A' : list[i].suppleant) : (list[i].titulaire == null ? 'N/A' : list[i].titulaire) )+
                            '</span>')+
                            (filter.grade > 0 ? '' : '<span class="super super-l12">'+list[i].niveau+'</span>')+
                            (filter.facname > 0 ? '' : '<span class="super super-l12">'+list[i].filiere+'</span>')+
                        '</span>'+
                    '</span>';
              horaire.find('.dayColumn[data-index="'+list[i].day+'"] .course-range').append(row);
          }
      },
      setCourseOptions: function(list, evaluation){
          var option = '<option value="">Tous les cours</option>';
          for(var i in list){
              option += '<option value="'+list[i].id+'">'+list[i].nom+'</option>';
          }
          $('.notes '+(evaluation ? '.evaluation' : '.palmares')+' .sort [name="course"]').html(option);
      },
      getStudentNote: function(cours,etu,annee){
          var note = null, notes = Modules.data.notes;
          for(var i in notes){
            if(notes[i]._annee_academique == annee && notes[i]._cours == cours && notes[i]._etudiant == etu){
                note = notes[i].note;
                break;
            }
          }
          return note;
      },
      print: function(view,outer,width,height){
          var width = set(width,900),
              height = set(height,700),
              printview = window.open("","","width="+width+",height="+height),
              outer = set(outer, true);
          if(printview == null){
              printview = window.open("","","width=900,height=700");
          }
          printview.document.write(
              '<link rel="stylesheet" href="../css/fonts.css">'+
              '<link rel="stylesheet" href="../css/globals.css" media="all">'+
              '<link rel="stylesheet" href="../css/web.css" media="all">'+
              '<link rel="stylesheet" href="../css/super.css">'+
              '<style>th,td{text-align: left !important;}</style>'+
              view[outer ? 'outerHTML' : 'innerHTML']
          );
          printview.document.close();
          printview.focus();
          setTimeout(function(){
              printview.print();
          },300);
          printview.onafterprint = function(){
              printview.close();
          }
      },
      isYearAbove: function(ya1, ya2){
        var a = this.getYearById(ya1),
            b = this.getYearById(ya2);
        return a != null && b != null && a.annee_debut >= b.annee_debut;
      },
      setNoteEditor: function(list,filter){
        var container = $('<div>'),
            cours = this.getCourseById(filter.course),
            val;
        if(cours == null) return;
        $('.notes .evaluation .header .name').text(cours.nom);
        $('.notes .evaluation .header .maximum').text(" Cœfficient "+cours.coefficient);
        for(var i in list){
            val = this.getStudentNote(filter.course, list[i].id, Modules.data.currentYear.id);
            val = val == null ? "" : val;
            container.append(
              '<div class="super super-l12 super-flex-center row" data-filter="'+list[i].prenom+" "+list[i].nom+" "+list[i].code+'">' +
                '<div class="super super-l2 code">' +
                    list[i].code+
                '</div>'+
                '<div class="super super-l8 name">' +
                    list[i].prenom+" "+list[i].nom+
                '</div>' +
                '<div class="super super-l2">' +
                    '<div class="super super-l12 field">' +
                    '   <label>Note</label>' +
                    '   <input class="input super-l12" '+(!Modules.allowModule(41) ? 'disabled readonly' : '')+' data-student="'+list[i].id+'" min="0" max="100" type="number" value="'+val+'">' +
                    '</div>'+
                '</div>' +
              '</div>'
            );
        }
        if(list.length) {
            $('.notes .evaluation .list').html(container.html()).find('.input').trigger('change');
        }else{
            $('.notes .evaluation .list').html(
                '<div class="super super-l12 empty-info super-flex-center">' +
                '  Aucun étudiant n\'a été enregistré !' +
                '</div>'
            )
        }
      },
      getStudentOverallSessionNote: function(fac, grade, etu, session, annee){
         var courses = this.getCourse(fac,grade,session,annee),
             total = 0, notes = 0, note;
         for(var i in courses){
             note = this.getStudentNote(courses[i].id, etu, annee);
             notes += (note == null ? 0 : note) * courses[i].coefficient;
             total += 100 * courses[i].coefficient;
         }
         return Math.round(notes / total * 10000) / 100;
      },
      setPalmares: function(list,filter){
          var grid = $('<table class="super super-l12 grid">'),
              summarize = filter.session == 0,
              oneCourse = !summarize && filter.course * 1 >= 0, total =  0, somme = 0, note = 0, rowCellCount = 0, insertion = 0, moyenne = 0,
              courses = oneCourse ? [this.getCourseById(filter.course)] : this.getCourse(filter.facname, filter.grade, filter.session, filter.academic);
          grid.append('<tbody></tbody>');
          var body = grid.find('tbody'), row;
          var c = oneCourse ? this.getCourseById(filter.course) : null;
          var fac = this.getFacById(filter.facname);
          $('.palmares .view-list .head .year .info').text(this.getYearById(filter.academic).academie);
          $('.palmares .view-list .head .text').text((oneCourse ? 'Notes session '+filter.session : 'Résultats '+(filter.session == 0 ? "finaux" : "session "+filter.session))+" | "+fac.grades[filter.grade].notation)
          $('.palmares .view-list .head .extras div').eq(0).text(fac.nom);
          $('.palmares .view-list .head .extras div').eq(1).text(oneCourse ? 'Prof. '+c.titulaire : '');
          $('.palmares .view-list .head .extras div').eq(2).text(oneCourse ? 'Cours : '+c.nom+' ( '+c.code+' )' : '');
          if(summarize){
              body.append(
                  '<tr>' +
                    '<th class="name code">Code</th>' +
                    '<th class="name">Nom</th>' +
                    '<th class="name">Prénom</th>' +
                    '<th>Session 1</th>' +
                    '<th>Session 2</th>' +
                    '<th>Moyenne</th>' +
                    '<th>Mention</th>' +
                  '</tr>'
              );
              rowCellCount = 4;
              body.append(
                  '<tr>' +
                      '<th></th>' +
                      '<th></th>' +
                      '<th></th>' +
                      '<th>sur 100</th>' +
                      '<th>sur 100</th>' +
                      '<th>sur 100</th>' +
                      '<th></th>' +
                  '</tr>'
              );
          }
          else{
              rowCellCount = 2;
              body.append(
                  '<tr>' +
                    '<th class="name code">Code</th>' +
                    '<th class="name">Nom</th>' +
                    '<th class="name">Prénom</th>'+
                    (function(){
                        var td = '';
                        for(var i in courses){
                            td += '<th>'+(!oneCourse ? courses[i].nom : 'Note sur 100')+'</th>';
                            total += courses[i].coefficient * 100;
                            rowCellCount++;
                        }
                        return td;
                    }())+
                    (!oneCourse ? '<th>Total</th>' : '') +
                    (!oneCourse ? '<th>Moyenne</th>' : '') +
                    (!oneCourse ? '<th rowspan="2">Mention</th>' : '') +
                  '</tr>'
              );
              if(!oneCourse) {
                  body.append(
                  '<tr class="bareme">' +
                          '<th></th>' +
                          '<th></th>' +
                          '<th></th>' +
                          (function () {
                              var td = '';
                              if (oneCourse) return '<td>Note sur 100</td>';
                              for (var i in courses) {
                                  td += '<th>' + courses[i].coefficient + '</th>';
                              }
                              return td;
                          }()) +
                          (!oneCourse ? '<th> sur ' + total + '</th>' : '') +
                          (!oneCourse ? '<th> sur 100</th>' : '') +
                          (!oneCourse ? '<th></th>' : '') +
                      '</tr>'
                  );
              }
          }
          for(var i in list){
              row = $('<tr class="studrow" data-filter="'+list[i].prenom+" "+list[i].nom+" "+list[i].code+'"></tr>');
              body.append(row);
              insertion = 2;
              somme = 0;
              row.append(
                  '<td class="name code">'+list[i].code+'</td>'+
                  '<td class="name">'+list[i].nom+'</td>'+
                  '<td class="name">'+list[i].prenom+'</td>'
              );
              if(summarize){
                  note = this.getStudentOverallSessionNote(filter.facname, filter.grade, list[i].id ,1, filter.academic);
                  somme += note;
                  row.append('<td>'+note+'</td>');
                  note = this.getStudentOverallSessionNote(filter.facname, filter.grade, list[i].id ,2, filter.academic);
                  somme +=note;
                  somme = Math.round(somme / 2 * 100) / 100;
                  row.append('<td>'+note+'</td>');
                  row.append('<td class="'+(somme >= 65 ? 'good' : 'bad')+'">'+somme+'</td>');
                  row.append('<td class="'+(somme >= 65 ? 'good' : 'bad')+'">'+(somme >= 65 ? 'Succès' : 'Echec')+'</td>');
              }
              else{
                  for(var j in courses){
                      note = this.getStudentNote(courses[j].id, list[i].id, filter.academic);
                      if(note != null){
                          somme += note * courses[j].coefficient;
                          insertion++;
                      }
                      row.append('<td>'+(note == null ? '' : note)+'</td>')
                  }
                  if(!oneCourse){
                      moyenne = Math.round(somme / total  * 10000) / 100;
                      row.append(
                          '<td>'+somme+'</td>' +
                          '<td class="'+(insertion == rowCellCount ? moyenne >= 65 ? 'good' : 'bad' : '')+'">'+(insertion == rowCellCount ? moyenne : '')+'</td>' +
                          '<td class="'+(insertion == rowCellCount ? moyenne >= 65 ? 'good' : 'bad' : '')+'">'+(insertion == rowCellCount ? moyenne >= 65 ? 'Succès' : 'Echec' : '' )+'</td>'
                      );
                  }
              }
          }
          $('.notes .palmares .list-view').html(grid);
      },
      setStudentPrintView: function(list, filter){
          if(list.length == 0) return;
          $('#printview .list').html('');
          var temp = $('<div class="list-printing">');
          $('#printview .year .info').text(this.getYearById(filter.year).academie);
          $('#printview .text').text("Liste des étudiants "+(filter.fac >= 0 ? " de "+this.getFacById(filter.fac).nom+(filter.grade >= 0 ? " niveau "+this.getFacById(filter.fac).grades[filter.grade].notation : "") : ""));
          if(list.length == 1){
              $('#printview .text').text('Fiche d\'étudiant');
              var metadata = $('.student .student-item[data-id="'+list[0].id+'"]').find('.metadata');
              metadata.addClass('student-item metadata').find('.actions').remove();
              metadata.html('<div class="metadatas super super-l12">'+metadata[0].outerHTML+'</div>');
              temp.append(
                  '<div class="super super-l12 " style="font-family: Poppins-SemiBold; font-size: 1.6em">'+list[0].prenom+" "+list[0].nom+'</div>'
              )
              temp.append(metadata[0].outerHTML);
          }
          else{
              var table = $('<table class="super super-l12">');
              table.append(
                  '<tr>' +
                      '<th></th>' +
                      '<th>Code</th>' +
                      '<th>Nom</th>' +
                      '<th>Prénom</th>' +
                      '<th>Sexe</th>' +
                      '<th>Téléphone</th>' +
                      '<th>Adresse</th>' +
                      (filter.fac < 1 ? '<th>Filière</th>' : '') +
                      (filter.grade < 1 ? '<th>Niveau</th>' : '') +
                      '<th>Date naissance</th>' +
                      '<th>Lieu naissance</th>' +
                      '<th>NIF</th>' +
                      '<th>NINU</th>' +
                      '<th>Personne de référence</th>' +
                      '<th>Numéro de personne de référence</th>' +
                  '</tr>'
              );
              var row;
              for(var i in list){
                  row = $('<tr>');
                  row.append(
                      '<td>'+(i*1+1)+'</td>' +
                      '<td>'+list[i].code+'</td>' +
                      '<td>'+list[i].nom+'</td>' +
                      '<td>'+list[i].prenom+'</td>' +
                      '<td>'+(list[i].sexe == "M" ? "Homme" : "Femme")+'</td>' +
                      '<td>'+list[i].telephone+'</td>' +
                      '<td>'+list[i].adresse+'</td>' +
                      (filter.fac < 1 ? '<td>'+list[i].niveau.filiere.nom+'</td>' : '') +
                      (filter.grade < 1 ? '<td>'+list[i].niveau.notation+'</td>' : '') +
                      '<td>'+list[i].date_naissance+'</td>' +
                      '<td>'+list[i].lieu_naissance+'</td>' +
                      '<td>'+list[i].nif+'</td>' +
                      '<td>'+list[i].ninu+'</td>'+
                      '<td>'+list[i].personne_ref+'</td>'+
                      '<td>'+list[i].telephone_ref+'</td>'
                  )
                  table.append(row);
              }
              temp.append(table);
          }
          $('#printview .list').html(temp);
      },
      setTeacherPrintView: function(list){
          if(list.length == 0) return;
          $('#printview .list').html('');
          var temp = $('<div class="list-printing">');
          $('#printview .year .info').text(Modules.data.currentYear.academie);
          $('#printview .text').text("Liste des professeurs ");
          if(list.length == 1){
              $('#printview .text').text('Fiche du professeur');
              var metadata = $('.teacher .teacher-item[data-id="'+list[0].id+'"]').find('.metadata');
              metadata.addClass('teacher-item metadata').find('.actions').remove();
              metadata.html('<div class="metadatas super super-l12">'+metadata[0].outerHTML+'</div>');
              temp.append(
                  '<div class="super super-l12 " style="font-family: Poppins-SemiBold; font-size: 1.6em">'+list[0].prenom+" "+list[0].nom+'</div>'
              )
              temp.append(metadata[0].outerHTML);
          }
          else{
              var table = $('<table class="super super-l12">');
              table.append(
                  '<tr>' +
                  '<th></th>' +
                  '<th>Code</th>' +
                  '<th>Nom</th>' +
                  '<th>Prénom</th>' +
                  '<th>Sexe</th>' +
                  '<th>Téléphone</th>' +
                  '<th>Adresse</th>' +
                  '<th>Date naissance</th>' +
                  '<th>Lieu naissance</th>' +
                  '<th>Status matrimonial</th>' +
                  '<th>NIF</th>' +
                  '<th>NINU</th>' +
                  '<th>Niveau d\'étude</th>' +
                  '<th>Poste occupé</th>' +
                  '<th>Salaire</th>' +
                  '</tr>'
              );
              var row;
              for(var i in list){
                  row = $('<tr>');
                  row.append(
                      '<td>'+(i*1+1)+'</td>' +
                      '<td>'+list[i].code+'</td>' +
                      '<td>'+list[i].nom+'</td>' +
                      '<td>'+list[i].prenom+'</td>' +
                      '<td>'+(list[i].sexe == "M" ? "Homme" : "Femme")+'</td>' +
                      '<td>'+list[i].telephone+'</td>' +
                      '<td>'+list[i].adresse+'</td>' +
                      '<td>'+list[i].date_naissance+'</td>' +
                      '<td>'+list[i].lieu_naissance+'</td>' +
                      '<td>'+list[i].status_matrimonial+'</td>' +
                      '<td>'+list[i].nif+'</td>' +
                      '<td>'+list[i].ninu+'</td>'+
                      '<td>'+list[i].niveau_etude+'</td>'+
                      '<td>'+(list[i].poste == null ? "Aucun" : list[i].poste.notation+(list[i].poste.filiere != null ? " de "+list[i].poste.filiere : ""))+'</td>'+
                      '<td>'+list[i].salaire+' HTG</td>'
                  )
                  table.append(row);
              }
              temp.append(table);
          }
          $('#printview .list').html(temp);
      }
    },
    dispatch: {
      fac: function(){
          var edit = false,
              currentViewData = null,
              currentGradeId = null,
              gradeForm = $('.grade-form');
          gradeForm.remove().removeClass('not-super');
          var facGraph = new Chart(document.querySelector('.administration .histogramme-filiere').getContext('2d'),{
             type: 'bar',
             data: {
                 labels: ['Étudiant', 'Etudiants actifs', 'Professeurs', 'Cours', 'Cours dispensés'],
                 datasets: [{
                     data: [0,0,0,0,0],
                     backgroundColor: [
                         'rgba(255, 99, 132, 0.2)',
                         'rgba(54, 162, 235, 0.2)',
                         'rgba(255, 206, 86, 0.2)',
                         'rgba(235,54,199,0.2)',
                         'rgba(128,255,86,0.2)'
                     ]
                 }]
             }
          });
          $('.administration')
          .on('click', '#addFac', function(){
              if(Form.isLocked(this)) return;
              var val = $('#newFacText').val(),
                  $this = this;
              if(val.length){
                  console.warn('[Add]',val);
                  edit = currentViewData != null;
                  DialogBox.setWith(
                      "ion-information",
                      !edit ? "Une nouvelle filière/faculté va être enregistrée ?" :
                      "Le nom de&nbsp;<b style='margin: 0 .4em'>"+currentViewData.nom+"</b>&nbsp;va être modifié !",
                  "yesno"
                  )
                  .show()
                  .onClose(function(e,r) {
                      if(!r){
                          if(currentFacIndex != null){
                              currentFacIndex = null;
                              $('#newFacText').val('');
                          }
                          return;
                      }
                      DialogBox.setWith("ion-load-d animate-spin","Requête en cours..", 'None')
                      .show();
                      Fetch.post('./submit', {
                          facname: val,
                          reqtoken: Modules.data.token,
                          requid: Modules.data.id,
                          facid: !edit ? null : currentViewData.id
                      }).then(function (r) {
                          Form.unlock($this);
                          edit = false;
                          currentViewData = null;
                          DialogBox.setWith("ion-android-done","Enregistrement fait avec succès !", "OK")
                          .onClose(function () {
                              History.reach();
                              $('.administration .card-tab').eq(1).trigger('click');
                          })
                      }).catch(function (e) {
                          Form.unlock($this);
                          edit = false;
                          DialogBox.setWith("ion-android-warning", e, "OK");
                      })
                  });
              }
          })
          .on('click', '.administration .fac-infos button.delete', function(){
              var data = currentViewData,
                  message = "Cette filière/faculté va être supprimée ! "+
                            "Cela entraînera en cascade la suppression des cours et des notes et les étudiants qui lui sont liées.<br>"+
                            "Avez-vous la certitude d'exécuter cette opération ?";
              DialogBox.setWith("ion-information",message, "yesno")
              .show()
              .onClose(function(e,r){
                  if(!r) return;
                  DialogBox.setWith("ion-load-d animate-spin","Requête en cours...", "none")
                  .show()
                  Fetch.post('./submit',{
                      reqtoken: Modules.data.token,
                      requid: Modules.data.id,
                      facdelid: data.id
                  }).then(function(){
                      DialogBox.setWith("ion-android-done","Suppression réussie !", "ok")
                      .onClose(function(){
                          History.reach();
                          $('.administration .card-tab').eq(1).trigger('click');
                      })
                  }).catch(function(e){
                      DialogBox.setWith("ion-android-warning",e, "ok")
                  })
              })
          })
          .on('click', '.administration .fac-infos button.edit', function(){
              $('#newFacText').val(currentViewData.nom).focus();
              $('.administration .adder .redo').show();
          })
          .on('click', '.administration .body item.faculty', function(){
              var index = $(this).attr('data-index'),
                  data = Modules.data.faculty[index];
              currentViewData = data;
              Boot.terminal.setFacUI(data);
              $('.administration .fac-infos .academic-list').find('option[selected]').removeAttr('selected');
              console.log('[Sele]',$('.administration .fac-infos .academic-list'));
              $('.administration .fac-infos .academic-list').each(function(){
                  $(this).find('option[value="'+Modules.data.currentYear.academie+'"]').attr('selected', '');
              })
              $('.administration .fac-infos .academic-list').trigger('change');
          })
          .on('change', '.administration .fac-infos .academic-list', function(){
              var stat = currentViewData.stats[$(this).val()],
                  dataset = [stat.student_total, stat.active_student_total, stat.teacher_total, stat.course_total, stat.given_course_total];
              Boot.terminal.updateChart(facGraph,[dataset]);
          })
          .on('click', '.add-grade',function(){
              if(currentGradeId == null){
                Form.reset(gradeForm);
              }
              FormPopup
              .html(gradeForm)
              .title("Niveau")
              .show()
              .onAction(function(submit, data){
                  if(submit){
                      if(Form.isSubmitable(data)){
                          DialogBox.setWith('ion-load-d animate-spin', 'Requête en cours...', 'none')
                          .show();
                          if(currentGradeId != null){
                              data['fac-grade-id'] = currentGradeId;
                          }
                          Fetch.post('./submit', extend(data,{
                              reqtoken: Modules.data.token,
                              requid: Modules.data.id,
                              'fac-id': currentViewData.id
                          })).then(function(){
                              DialogBox.setWith('ion-android-done green', 'Niveau académique enregistré', 'ok');
                              currentViewData = Boot.terminal.updateCurrentFacData(currentViewData);
                              Boot.terminal.setFacUI(currentViewData);
                              currentGradeId = null;
                              FormPopup.hide();
                          }).catch(function(e){
                              DialogBox.setWith('ion-android-warning', e, 'ok');
                          });
                      }
                      else{
                          DialogBox.setWith('ion-android-warning', 'Veuillez remplir les champs correctement !', 'ok');
                      }
                  }
                  else{
                    currentGradeId = null;
                  }
              })
          })
          .on('click', '.adder .redo', function(){
              edit = false;
              $('#newFacText').val('');
              $(this).hide();
          })
          .on('click', '.administration .fac-infos .edit-grade', function(){
              currentGradeId = $(this).attr('data-index');
              Form.reset(gradeForm, {
                  'fac-grade-value': currentViewData.grades[currentGradeId].niveau,
                  'fac-grade-notation': currentViewData.grades[currentGradeId].notation
              });
              $('.administration .add-grade').trigger('click');
              gradeForm.find('.input').trigger('focus');
          })
          .on('click', '.administration .fac-infos .delete-grade', function(){
              var message = "Ce niveau académique va être supprimé ! "+
                      "Cela entraînera en cascade la suppression des cours et des notes et les étudiants qui lui sont liées.<br>"+
                      "Avez-vous la certitude d'exécuter cette opération ?",
                  id = $(this).attr('data-index');
              DialogBox.setWith("ion-information",message, "yesno")
              .show().onClose(function(e,r){
                  if(!r) return;

                  DialogBox.setWith("ion-load-d animate-spin","Requête en cours..", 'None')
                  .show();
                  Fetch.post('./submit', {
                      requid: Modules.data.id,
                      reqtoken: Modules.data.token,
                      'fac-grade-delid': id
                  }).then(function(){
                      DialogBox.setWith("ion-android-done","Suppression faite avec succès !", "OK")
                      .onClose(function () {
                          currentViewData = Boot.terminal.updateCurrentFacData(currentViewData);
                          Boot.terminal.setFacUI(currentViewData);
                          FormPopup.hide();
                      })
                  }).catch(function(e){
                      DialogBox.setWith("ion-android-warning", e, "OK");
                  })
              })
              var data = currentViewData.grades[$(this).attr('data-index')];
          })
      },
      poste: function(){
          var posteIndex = null,
              posteData = null;
          $('.administration')
          .on('click', '#addPoste', function(){
              if(Form.isLocked(this)) return;
              var data = Form.serialize($('#posteForm')),
                  $this = this,
                  linked = data.poste_linked;
              delete data.poste_linked;
              if(Form.isSubmitable(data)){
                 data.poste_linked = linked;
                 Form.lock($this);
                 DialogBox.setWith("ion-information",
             posteIndex == null ? "Un nouveau poste va être ajouté !" :
                    "Le poste &nbsp;<b>"+posteData.notation+(posteData.filiere != null ? " de "+posteData.filiere : "")+
                    "</b>&nbsp; va être modifié !",
                     "yesno")
                 .show().onClose(function(e,r) {
                     if(!r) return;
                     DialogBox.setWith("ion-load-d animate-spin", "Requête en cours...", "none")
                     .show()
                     if(posteIndex != null){
                         data.poste_id = Modules.data.hierarchy[posteIndex].id;
                     }
                     Fetch.post('./submit', extend(data, {
                         requid: Modules.data.id,
                         reqtoken: Modules.data.token
                     })).then(function () {
                         Form.unlock($this);
                         DialogBox.setWith(
                             "ion-android-done",
                             posteIndex != null ? "Poste modifié avec succès !" : "Nouveau poste enregistré !",
                             "ok")
                         .show().onClose(function(){
                             History.reach();
                         })
                     }).catch(function (e) {
                         Form.unlock($this);
                         DialogBox.setWith("ion-android-warning", e, "ok")
                         .show()
                     })
                 });
              }
              else{

              }
          })
          .on('change', '#posteForm .input', function(){
              $('#addPoste')[Form.isSubmitable(Form.serialize($('#posteForm'))) ? 'removeClass' : 'addClass']('disabled')
          })
          .on('input', '#posteForm .input', function(){
              $('#addPoste')[Form.isSubmitable(Form.serialize($('#posteForm'))) ? 'removeClass' : 'addClass']('disabled')
          })
          .on('click', '.administration .body item.poste button.delete', function(){
              var index = $(this).attr('data-index'),
                  data = Modules.data.hierarchy[index];
              DialogBox.setWith("ion-information","Ce poste va être supprimé !", "yesno")
                  .show()
                  .onClose(function(e,r){
                      if(!r) return;
                      DialogBox.setWith("ion-load-d animate-spin","Requête en cours...", "none")
                          .show()
                      Fetch.post('./submit',{
                          reqtoken: Modules.data.token,
                          requid: Modules.data.id,
                          postedelid: data.id
                      }).then(function(){
                          DialogBox.setWith("ion-android-done","Suppression réussie !", "ok")
                              .onClose(function(){
                                  History.reach();
                              })
                      }).catch(function(e){
                          DialogBox.setWith("ion-android-warning",e, "ok")
                      })
                  })
          })
          .on('click', '.administration .body item.poste button.edit', function(){
              posteIndex = $(this).attr('data-index');
              posteData = Modules.data.hierarchy[posteIndex];
              Boot.terminal.setPostForm(posteData);
          })
          .on('click', '.administration .body item.poste .name', function(){
              // var index = $(this).attr('data-index'),
              //     data = Modules.data.faculty[index];
              // currentViewData = data;
              // Boot.terminal.setFacUI(data);
              // $('.administration .academic-list').trigger('change');
          })
      },
      updateInfo: function(data, ths){
        return new Promise(function(res){
            console.log('[PRV]',Modules.data.privileges.split(','));
            DialogBox.setWith('ion-load-d animate-spin', "Requête en cours...", 'none')
            .show();
            ths.params(extend(data,{
                url: './submit',
                fileIndexName: 'usr_upl_avatar',
                uploadedFileIndexName: 'usr_avatar',
                requid: Modules.data.id,
                reqtoken: Modules.data.token,
                usr_access: Modules.data.privileges.split(','),
                usr_id: Modules.data.id
            })).start().then(function(r){
                res();
                Modules.updateData(r.data);
                Modules.setAppBar(Modules.data);
                History.reach();
                DialogBox.setWith("ion-android-done green","Vos informations personnelles ont été modifiées avec succès !", 'Ok')
            }).catch(function(e){
                res();
                DialogBox.setWith("ion-android-warning",e, 'ok');
            });
        });
      }
    },
    "/academic-year": function(){
        "use strict";
        var ctx = $('.diagramme')[0].getContext('2d'),
            data = $('.academic item.active'),
            current = data.length ? Modules.data.academic[data.attr('data-index')] : null;
        data = !data.length ? [0,0,0,0] : [current.nb_prof, current.nb_etu, current.nb_filiere, current.nb_cours];
        this.terminal.setAYUI(current);
        var myChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Professeurs', 'Étudiants', 'Filières', 'Cours'],
                datasets: [{
                    data: data,
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.2)',
                        'rgba(54, 162, 235, 0.2)',
                        'rgba(255, 206, 86, 0.2)',
                        'rgba(75, 192, 192, 0.2)'
                    ],
                    borderColor: [
                        'rgba(255, 99, 132, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(75, 192, 192, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
        Modules.search = function(e,reg){
            $('.academic item').each(function(){
                if(new RegExp(reg, "i").test($(this).attr('data-filter')) || reg.length == 0){
                    $(this).show();
                }
                else{
                    $(this).hide();
                }
            })
        };
        //on
        $('.academic')
        .on('change', '#Aka-yearForm .input', function(){
            console.log('[Text]', $(this).val(), new Date($(this).val()))
            var input = $(this),
                date = input.val();
            if(input.hasClass('begin')){
                $('.anneeAcademique .begin').text(date.split('-')[0]);
            }
            if(input.hasClass('end')){
                if(detect.dateComparison($('#Aka-yearForm .begin').val(), $('#Aka-yearForm .end').val()) > 0){
                    AlertBox.set('.anneeAka-year-alert')
                        .type('warn')
                        .text("La <b>date de début</b> doit être inférieur à la <b>date de fin</b> !")
                        .icon("las la-exclamation")
                        .show();
                    input.val('');
                }
                else{
                    AlertBox.set('.anneeAka-year-alert').hide();
                }
                $('.anneeAcademique .end').text(date.split('-')[0]);
            }
        })
        .on('click', 'item', function(){
            $('.academic item.active').removeClass('active');
            $(this).addClass('active');
            current = Modules.data.academic[$(this).attr('data-index')];
            Boot.terminal.setAYUI(current);
            myChart.data.datasets[0].data = [current.nb_prof, current.nb_etu, current.nb_filiere, current.nb_cours];
            myChart.update();
        })
        .on('click', '.aka-form', function(){
            $('#newYear').attr('data-index', '-1');
            Boot.terminal.resetAYForm();
        })
        .on('click', '#editYear', function(){
            $('.aka-form').trigger('click');
            var index = $('.academic item.active').attr('data-index')
            Boot.terminal.resetAYForm(extend(Modules.data.academic[index],{
                index : index
            }));
        })
        .on('click', '#passYear', function(){
            var $this = $(this);
            DialogBox
            .setWith('ion-information gray', ' Avez-vous la certitude de passer les étudiants ?', 'yesno')
            .show()
            .onClose(function(e,r){
                if(r){
                    DialogBox.set("<icon class='ion-load-d animate-spin'></icon> Requête en cours...", 'NONE').show();
                    Fetch.post('./submit', {
                        reqtoken: Modules.data.token,
                        requid: Modules.data.id,
                        akpass: true,
                        akid: Modules.data.currentYear.id
                    }).then(function(r){
                        History.reach();
                        DialogBox.setWith('ion-android-done green',r.message, 'OK');
                    }).catch(function(e){
                        $this.trigger('rollback')
                        DialogBox.set("<icon class='ion-android-warning rounded'></icon> "+e, 'OK');
                    })
                }
            })
        })
        .on('click', '#newYear', function(){
            if(Form.isLocked(this)) return;
            var data = Form.serialize($('#Aka-yearForm')),
                $this = this;
            if(Form.isSubmitable(data)){
                Form.lock(this);
                AlertBox.set('.anneeAka-year-alert')
                    .type('normal')
                    .text("Requête en cours!")
                    .icon("ion-load-d")
                    .show();
                if($(this).attr('data-index') != '-1'){
                    data.reqeditid = Modules.data.academic[$(this).attr('data-index')].id;
                }
                Fetch.post('./submit', extend(data,{
                    reqtoken: Modules.data.token,
                    requid: Modules.data.id,
                })).then(function(r){
                    Boot.terminal.resetAYForm();
                    Form.unlock($this);
                    AlertBox.set('.anneeAka-year-alert')
                        .type('success')
                        .text("Année académique enregistrée avec succès")
                        .icon("ion-android-done")
                    History.reach();
                }).catch(function(e){
                    Form.unlock($this);
                    AlertBox.set('.anneeAka-year-alert')
                        .type('fail')
                        .text(e)
                        .icon("ion-alert")
                })
            }
            else{
                AlertBox.set('.anneeAka-year-alert')
                    .type('warn')
                    .text("Veuillez tout remplir correctement !")
                    .icon("ion-compose")
                    .show();
            }
        })
    },
    "/admin": function(){
        var cm = new Card('.administration');
        cm.onSwitch(function(index){
           Boot.terminal[index == 1 ? 'setPosteUI' : 'setFacUI'](null);
           Modules.search = function(e,reg){
               $('.administration .'+(index != 1 ? 'poste' : 'fac')+' item').each(function(){
                   if(new RegExp(reg, "i").test(index != 1 ? $(this).find('.name').text() : $(this).text()) || reg.length == 0){
                       $(this).show();
                   }
                   else{
                       $(this).hide();
                   }
               })
           };
        });
        this.dispatch.fac();
        this.dispatch.poste();
        Modules.search = function(e,reg){
            $('.administration .poste item').each(function(){
                if(new RegExp(reg, "i").test($(this).find('.name').text()) || reg.length == 0){
                    $(this).show();
                }
                else{
                    $(this).hide();
                }
            })
        };
    },
    "/teacher": function(){
        var edit = false,
            currentTeacher = null,
            linkForm = $('#linkingForm'),
            avatarUploader = new ThunderSpeed(), activeList = [],
            form = $('#teacherForm');
        linkForm.removeClass('not-super').remove();
        avatarUploader
        .filterType(['jpeg', 'jpg', 'png'])
        .watch('#picture-file')
        .on('choose', function(e){
            console.log('[Instance]', e.files());
            var files = e.files();
            if(files.length){
                var file = files[0];
                if(file.rawsize > 2 * 1024 * 1024){
                    DialogBox.setWith('ion-android-warning', 'Vous devez choisir un fichier image d\'une taille faisant moins de 2 MB', 'ok')
                    .show();
                    return;
                }
                file.getDataURL().then(function(result){
                    $('#teacherForm avatar').html('').css('background-image', 'url(' + result + ')')
                });
            }
        }).on('progress', function(e){
            DialogBox.setWith("ion-load-d animate-spin","Requête en cours [ " + e.percent + "% ]", 'None');
        });
        Modules.search = function(e,reg){
            var total = 0;
            activeList = [];
          $('.teacher item').each(function(){
              if(new RegExp(reg, "i").test($(this).attr('data-filter')) || reg.length == 0){
                  $(this).show();
                  total++;
                  activeList.push(Boot.terminal.getTeacherById($(this).attr('data-id')));
              }
              else{
                  $(this).hide();
              }
          });
          $('.teacher .print-teacher')[total ? 'show': 'hide']();
        };
        $('.teacher')
        .on('click', '#newTeacher', function(){
            var data = Form.serialize($('#teacherForm')),
                poste = data.th_hierarchy,
                memo = data.th_memo;
                data.th_code = $('.teacher .identification .code').text();
            delete data.th_hierarchy;
            delete data.th_memo;
            if(!edit && avatarUploader.files().length == 0){
                DialogBox.setWith('ion-android-warning red', 'Vous devez choisir une photo d\'identité !', 'ok').show();
                return;
            }
            if(Form.isSubmitable(data)){
                AlertBox.set('.teacher-alert').hide();
                data.th_hierarchy = poste;
                data.th_memo = memo;
                DialogBox.setWith("ion-load-d animate-spin","Requête en cours..", 'None')
                .show();
                avatarUploader.params(extend(data,{
                    url: './submit',
                    fileIndexName: 'th_upl_avatar',
                    uploadedFileIndexName: 'th_avatar',
                    reqtoken: Modules.data.token,
                    requid: Modules.data.id,
                    th_id: !edit ? null : currentTeacher.id,
                    th_identity: !edit ? null : currentTeacher.identite,
                }))
                .start().then(function(r){
                    Modules.updateData(r.data);
                    currentTeacher = null;
                    $('.teacher .teach-form').trigger('click');
                    DialogBox.setWith("ion-android-done",(edit ? "Modification faite": "Enregistrement fait")+" avec succès !", "OK")
                    .onClose(function () {
                        console.log("[Terminate]");
                        History.reach();
                        edit = false;
                    })
                }).catch(function(e){
                    DialogBox.setWith("ion-android-warning", e, "OK");
                });
            }
            else{
                AlertBox.set('.teacher-alert')
                .type('warn')
                .text("Veuillez tout remplir correctement !")
                .icon("ion-compose")
                .show();
            }
        })
        .on('input', '#teacherForm .input', function(){
            var data = Form.serialize($('#teacherForm')),
                code = '';
            if(data.th_nom.length && data.th_prenom.length){
                code = Boot.terminal.getCode(data.th_nom, data.th_prenom);
            }
            $('#teacherForm .code').text(code.toUpperCase());
        })
        .on('click', '.teacher-item .summary', function(){
            var deploy = !$(this).parent().hasClass('open');
            console.log('[Clicked !]');
            $('.teacher-item.open').removeClass('open').find('.see span').text('Plus de détails');
            if(deploy){
                $(this).parent().addClass('open').find('.see span').text('Moins de détails');
            }
        })
        .on('click', '.teach-form', function(){
            edit = false;
            Boot.terminal.setTeacherUI(form);
            avatar = null;
            form.find('[type="file"]').val('')
            form.find('avatar').css('background-image', 'unset').html('<icon class="las la-user"></icon>');
        })
        .on('click', '.teacher-item .metadata .edit', function(){
            currentTeacher = Modules.data.teacher[$(this).attr('data-index')];
            $('.teacher-item[data-id="'+currentTeacher.id+'"]').find('.summary').trigger('click');
            $('.teacher .teach-form').trigger('click');
            edit = true;
            Boot.terminal.setTeacherUI(form, {
                th_nom : currentTeacher.nom,
                th_prenom: currentTeacher.prenom,
                th_sexe: currentTeacher.sexe,
                th_status : currentTeacher.status_matrimonial,
                th_address : currentTeacher.adresse,
                th_phone : currentTeacher.telephone,
                th_birthplace : currentTeacher.lieu_naissance,
                th_birthdate : currentTeacher.date_naissance,
                th_skill : currentTeacher.niveau_etude,
                th_email : currentTeacher.email,
                th_hierarchy : currentTeacher.poste != null ? currentTeacher.poste.id : 0,
                th_sales : currentTeacher.salaire,
                th_nif : currentTeacher.nif,
                th_ninu : currentTeacher.ninu,
                th_memo : currentTeacher.memo,
                th_code : currentTeacher.code
            });
            if(currentTeacher.photo != null) {
                form.find('avatar').css('background-image', 'url(../assets/avatars/'+currentTeacher.photo+')').html('');
            }
        })
        .on('click', '.teacher-item .metadata .delete', function(){
            var data = Modules.data.teacher[$(this).attr('data-index')],
                t = len(data.cours),
                message = t == 0 ? "Avez-vous la certitude de supprimer ce professeur ?" :
                    "Il faudra songer à remplacer ce professeur pour "+(t > 1 ? "les" : "le")+" cours qu'il dispensait.<br>"+
                    "Avez-vous encore la certitude de supprimer ce professeur ?";
            DialogBox.setWith('ion-android-warning', message, 'yesno')
            .show()
            .onClose(function(e,r){
               if(r){
                   DialogBox.setWith('ion-load-b', "Requête en cours...", 'none')
                   .show()
                   Fetch.post('./submit',{
                       th_del_id : data.id,
                       reqtoken: Modules.data.token,
                       requid: Modules.data.id
                   }).then(function(){
                       DialogBox.setWith('ion-android-done', "Suppression réussie !", 'ok')
                       History.reach();
                   }).catch(function(e){
                       DialogBox.setWith('ion-android-warning', e, 'ok')
                   })
               }
            });
        })
        .on('change', '.state .switch', function(){
            var $this = $(this),
                data = Modules.data.teacher[$(this).attr('data-index')];
            DialogBox
            .setMessage("<icon class='rounded ion-information gray'></icon> L'état de ce professeur va être modifié !")
            .setType('YESNO')
            .show()
            .onClose(function(e,r){
                if(!r){
                    $this.trigger('rollback')
                }
                else{
                    console.log()
                    DialogBox.set("<icon class='ion-load-d animate-spin'></icon> Requête en cours...", 'NONE').show();
                    Fetch.post('./submit', {
                        th_id: data.id,
                        reqtoken: Modules.data.token,
                        requid: Modules.data.id,
                        th_state: $this.val().toUpperCase()[0]
                    }).then(function(r){
                        DialogBox.set("<icon class='ion-android-done rounded'></icon> Mise en état réussie !", 'OK');
                    }).catch(function(e){
                        $this.trigger('rollback')
                        DialogBox.set("<icon class='ion-android-warning rounded'></icon> "+e, 'OK');
                    })
                }
            })
        })
        .on('click', '.avatar-chooser', function(){
            $('#picture-file').click();
        })
        .on('click', '.print-teacher', function(){
            Boot.terminal.setTeacherPrintView(activeList);
            Boot.terminal.print($('#printview')[0],false);
        })
        .on('click', '.account-linking', function(){
            var teacher = Modules.data.teacher[$(this).attr('data-index')];
            FormPopup.title("Création de compte").html(linkForm).show().onAction(function(submit,data){
                if(submit){
                    if(Form.isSubmitable(data)){
                        data.requid = Modules.data.id;
                        data.reqtoken = Modules.data.token;
                        data.th_id = teacher.id;
                        DialogBox.setWith("ion-load-d animate-spin","Requête en cours...", "none").show();
                        console.log('[Data]',data);
                        Fetch.post('./submit',data).then(function(){
                            DialogBox.setWith("ion-android-done","La création du compte pour le professeur a été un succès !", "OK")
                            .onClose(function () {
                                History.reach();
                                FormPopup.hide();
                            })
                        }).catch(function(e){
                            DialogBox.setWith("ion-android-warning", e, "OK");
                        });
                    }
                    else{
                        DialogBox.set('ion-android-warning red', "Veuillez remplir correctement les champs !", 'ok')
                        .show();
                    }
                }
            })
        })
    },
    "/student": function(){
        var activeList = [], filter = {},
            form = $('#studentForm'),
            avatar = null,
            currentStudent = null,
            ts = {
                avatar : new ThunderSpeed(),
                excel : new ThunderSpeed()
            };
        ts.avatar
        .filterType(['jpeg', 'jpg', 'png'])
        .watch('#picture-file')
        .on('choose', function(e){
            console.log('[Instance]', e.files());
            var files = e.files();
            if(files.length){
                var file = files[0];
                if(file.rawsize > 2 * 1024 * 1024){
                    DialogBox.setWith('ion-android-warning', 'Vous devez choisir un fichier image d\'une taille faisant moins de 2 MB', 'ok')
                    .show();
                    return;
                }
                file.getDataURL().then(function(result){
                    $('#studentForm avatar').html('').css('background-image', 'url(' + result + ')')
                });
            }
        }).on('progress', function(e){
            DialogBox.setWith("ion-load-d animate-spin","Requête en cours [ " + e.percent + "% ]", 'None');
        });
        ts.excel.filterType(['xls','xlsx'])
        .watch("#choose-file")
        .on('choose', function(e){
            var files = e.files();
            if(files.length){
                DialogBox.setWith("ion-load-d animate-spin","Préparation du fichier d'importation...", 'None')
                .show();
                files[0].getDataURL().then(function(){
                    DialogBox.setWith("ion-load-d animate-spin","Requête en cours..", 'None');
                    e.params({
                        url: './submit',
                        fileIndexName: 'st_excel',
                        uploadedFileIndexName: 'st_list',
                        requid: Modules.data.id,
                        reqtoken: Modules.data.token
                    })
                    .start()
                    .then(function(r){
                        Modules.updateData(r.data);
                        Form.unlock($('.file-register'));
                        DialogBox.setWith("ion-android-done green","Importation réussie !", 'ok')
                        .onClose(function () {
                            History.reach();
                        })
                    }).catch(function(e){
                        Form.unlock($('.file-register'));
                        DialogBox.setWith("ion-android-warning red",e, 'ok');
                    });
                })
            }
        }).on('progress', function(e){
            DialogBox.setWith("ion-load-d animate-spin","Requête en cours [ " + e.percent + "% ]", 'None');
        })

        Modules.search = function(e,reg){
            var grade = $('.student .sort .grade .input').val(),
                fac =  $('.student .sort .faculty .input').val(),
                year =  $('.student .sort .academic .input').val(),
                data = {}, total = 0,
                display;
            activeList = [];
            grade = grade.length ? grade * 1 : -1;
            fac = fac.length ? fac * 1 : -1;
            filter = {
                fac: fac,
                year: year,
                grade: grade
            };
            $('.student .list .student-item').each(function(){
                display = false;
                data = {
                  fac : $(this).attr('data-fac') * 1,
                  grade : $(this).attr('data-grade') * 1,
                  year : $(this).attr('data-year') * 1,
                  filter : $(this).attr('data-filter')
                };
               if( Boot.terminal.isYearAbove(year, data.year) &&
                   (fac < 1 || fac == data.fac) &&
                   (grade < 1 || grade == data.grade) &&
                   (e.length == 0 || new RegExp(reg,"i").test(data.filter))
               ){
                 display = true;
                 total++;
                 activeList.push(Boot.terminal.getStudentById($(this).attr('data-id')));
               }
               $('.student .list .empty-info')[display ? 'addClass' : 'removeClass']('not-super');
               $(this)[display ? 'removeClass' : 'addClass']('not-super');
            });
            $('.student .sort .print-student')[total ? 'removeClass' : 'addClass']('disabled');
            $('.student .list .empty-info')[total ? 'addClass' : 'removeClass']('not-super');
        };

        $('.student')
        .on('change', '.faculty .input', function(){
            var id = $(this).val(),
                gradeSelect = $(this).parent().parent().find('.grade .input'),
                options = '<option value="">Tous les Niveaux</option>',
                grades = Boot.terminal.getGradesOf(id);
            for(var i in grades){
                options += '<option value="'+i+'">'+grades[i].notation+'</option>';
            }
            gradeSelect.html(options);
        })
        .on('change', '.sort .input[name]', function(){
            Modules.searchTrigger();
        })
        .on('click', '#newStudent', function(){
            var data = Form.serialize(form),
                memo = data.st_memo;
                data.st_code = $('.student .identification .code').text();
            delete data.st_memo;
            if(!edit && ts.avatar.files().length == 0){
                DialogBox.setWith('ion-android-warning red', 'Vous devez choisir une photo d\'identité !', 'ok').show();
                return;
            }
            if(Form.isSubmitable(data)){
                AlertBox.set('.student-alert').hide();
                data.st_memo = memo;
                console.log('[Data]',data);
                DialogBox.setWith("ion-load-d animate-spin","Requête en cours..", 'None')
                .show();
                ts.avatar.params(extend(data,{
                    url: './submit',
                    fileIndexName: 'st_avatar',
                    uploadedFileIndexName: 'st_upl_avatar',
                    reqtoken: Modules.data.token,
                    requid: Modules.data.id,
                    st_id: !edit ? null : currentStudent.id,
                    st_avatar: avatar
                }))
                .on('progress', function(e){
                    console.log('[E]',e);
                })
                .start()
                .then(function(r){
                    currentStudent = null;
                    avatar = null;
                    Modules.updateData(r.data);
                    $('.student .student-form').trigger('click');
                    $('.student-item.open .summary').trigger('click');
                    DialogBox.setWith("ion-android-done",(edit ? "Modification faite": "Enregistrement fait")+" avec succès !", "OK")
                    .onClose(function () {
                            edit = false;
                            History.reach();
                        })
                })
                .catch(function(e){
                    DialogBox.setWith("ion-android-warning", e, "OK");
                });
                // Fetch.post('./submit', extend(data,{
                //     reqtoken: Modules.data.token,
                //     requid: Modules.data.id,
                //     st_id: !edit ? null : currentStudent.id,
                //     st_avatar: avatar
                // }))
                // .then(function(r){
                //     currentStudent = null;
                //     avatar = null;
                //     $('.student .student-form').trigger('click');
                //     $('.student-item.open .summary').trigger('click');
                //     DialogBox.setWith("ion-android-done",(edit ? "Modification faite": "Enregistrement fait")+" avec succès !", "OK")
                //     .onClose(function () {
                //         edit = false;
                //         History.reach();
                //     })
                // })
                // .catch(function(e){
                //     DialogBox.setWith("ion-android-warning", e, "OK");
                // });
            }
            else{
                AlertBox.set('.student-alert')
                    .type('warn')
                    .text("Veuillez tout remplir correctement !")
                    .icon("ion-compose")
                    .show();
            }
        })
        .on('input', '#studentForm .input', function(){
            var data = Form.serialize(form),
                code = '';
            if(data.st_nom.length && data.st_prenom.length){
                code = Boot.terminal.getCode(data.st_nom, data.st_prenom);
            }
            $('#studentForm .code').text(code.toUpperCase());
        })
        .on('click', '.student-item .summary', function(){
            var deploy = !$(this).parent().hasClass('open');
            $('.student-item.open').removeClass('open').find('.see span').text('Plus de détails');
            if(deploy){
                $(this).parent().addClass('open').find('.see span').text('Moins de détails');
                // console.log({
                //     parent: $(this).parent().parent(),
                //     meta: $(this).parent().find('.metadata')
                // })
            }
        })
        .on('click', '.student-form', function(){
            edit = false;
            Boot.terminal.setStudentUI(form);
            avatar = null;
            form.find('[type="file"]').val('')
            form.find('avatar').css('background-image', 'unset').html('<icon class="las la-user"></icon>');
        })
        .on('click', '.student-item .metadata .edit', function(){
            currentStudent = Modules.data.student[$(this).attr('data-index')];
            $('.student .student-form').trigger('click');
            edit = true;
            Boot.terminal.setStudentUI(form, {
                st_nom : currentStudent.nom,
                st_prenom: currentStudent.prenom,
                st_sexe: currentStudent.sexe,
                st_person_ref: currentStudent.personne_ref,
                st_phone_ref: currentStudent.telephone_ref,
                st_address : currentStudent.adresse,
                st_phone : currentStudent.telephone,
                st_birthplace : currentStudent.lieu_naissance,
                st_birthdate : currentStudent.date_naissance,
                st_fac: currentStudent.niveau.filiere.id,
                st_skill : currentStudent._niveau,
                st_email : currentStudent.email,
                st_nif : currentStudent.nif,
                st_ninu : currentStudent.ninu,
                st_memo : currentStudent.memo,
                st_code : currentStudent.code
            });
            form.find('[name="st_skill"]').val(currentStudent._niveau).trigger('change');
            if(currentStudent.photo != null) {
                form.find('avatar').css('background-image', 'url(../assets/avatars/'+currentStudent.photo+')').html('');
            }
        })
        .on('click', '.student-item .metadata .delete', function(){
            var data = Modules.data.student[$(this).attr('data-index')],
                message = "En supprimant cet étudiant, vous supprimez aussi les notes qui lui sont liées (s'il y en a)<br>"+
                    "Avez-vous encore la certitude de supprimer cet étudiant ?";
            DialogBox.setWith('ion-android-warning', message, 'yesno')
            .show()
            .onClose(function(e,r){
                    if(r){
                        DialogBox.setWith('ion-load-b', "Requête en cours...", 'none')
                        .show()
                        Fetch.post('./submit',{
                            st_del_id : data.id,
                            reqtoken: Modules.data.token,
                            requid: Modules.data.id
                        }).then(function(){
                            DialogBox.setWith('ion-android-done', "Suppression réussie !", 'ok')
                            History.reach();
                        }).catch(function(e){
                            DialogBox.setWith('ion-android-warning', e, 'ok')
                        })
                    }
                });
        })
        .on('change', '.state .switch', function(){
            var $this = $(this),
                data = Modules.data.student[$(this).attr('data-index')];
            DialogBox
                .setMessage("<icon class='rounded ion-information gray'></icon> L'état de cet étudiant va être modifié !")
                .setType('YESNO')
                .show()
                .onClose(function(e,r){
                    if(!r){
                        $this.trigger('rollback')
                    }
                    else{
                        console.log()
                        DialogBox.set("<icon class='ion-load-d animate-spin'></icon> Requête en cours...", 'NONE').show();
                        Fetch.post('./submit', {
                            st_id: data.id,
                            reqtoken: Modules.data.token,
                            requid: Modules.data.id,
                            st_state: $this.val().toUpperCase()[0]
                        }).then(function(r){
                            DialogBox.set("<icon class='ion-android-done rounded'></icon> Mise en état réussie !", 'OK');
                        }).catch(function(e){
                            $this.trigger('rollback')
                            DialogBox.set("<icon class='ion-android-warning rounded'></icon> "+e, 'OK');
                        })
                    }
                })
        })
        .on('click', '.print-student', function(){
            if($(this).hasClass('disabled')) return;
            Boot.terminal.setStudentPrintView(activeList, filter);
            Boot.terminal.print($('#printview')[0],false);
        })
        .on('click', '.avatar-chooser', function(){
            $('#picture-file').click();
        })
        .on('click', '.file-register', function(){
            if(Form.isLocked(this)) return;
            var $this = this;
            DialogBox.setWith('las la-question', 'Voulez-vous faire une importation par un fichier Excel', 'yesno')
            .show().onClose(function(e,submit){
                if(submit){
                    $('#choose-file').click();
                }
            });
        })
        .on('change', '#choose-file', function(){
            var $this = $(this),
                file = this.files[0],
                fr = new FileReader();
            $(this).val('');
            if(file == undefined) {
                console.log('[Invalid !]')
                Form.unlock($('.file-register'));
                return;
            }

            Form.lock($('.file-register'));

            if(!/\.xls$/i.test(file.name)){
                $(this).val('');
                DialogBox.setWith('ion-android-warning', 'Vous devez choisir un fichier document de type *.xls', 'ok')
                    .show();
                return;
            }
            DialogBox.setWith("ion-load-d animate-spin","Préparation du fichier d'importation...", 'None')
            .show();
            fr.onload = function(){
                avatar = fr.result;
                DialogBox.setWith("ion-load-d animate-spin","Requête en cours..", 'None')
                Fetch.post('./submit', {
                    requid: Modules.data.id,
                    reqtoken: Modules.data.token,
                    st_list: avatar
                }).then(function(){
                    Form.unlock($('.file-register'));
                    $this.val('');
                    DialogBox.setWith("ion-android-done green","Importation réussie !", 'ok')
                    .onClose(function () {
                        History.reach();
                    })
                }).catch(function(e){
                    Form.unlock($('.file-register'));
                    $this.val('');
                    DialogBox.setWith("ion-android-warning red",e, 'ok');
                });
            }
            fr.readAsDataURL(file);
        })
    },
    "/course": function(){
        var scheduler = [],
            form = $('#courseForm'),
            currentCourse = null,
            hourId = 0,
            edit = false;
        Modules.search = function(e,reg){
            var grade = $('.course .sort .grade .input').val(),
                fac =  $('.course .sort .faculty .input').val(),
                ya =  $('.course .sort .academic .input').val(),
                session =  $('.course .sort .session .input').val(),
                existInYa = false,
                data = {}, total = 0, visibleTotal = 0;
            grade = set(grade, '').length ? grade * 1 : -1;
            fac = fac.length ? fac * 1 : -1;
                $('.course .course-list .bloc').each(function(){
                    total = 0;
                    data.fac = Boot.terminal.getFacById($(this).attr('data-id'));
                    if(fac < 1 || fac == data.fac.id ){
                        $(this).find('.course-item').each(function(){
                            var item = $(this);
                            data.course = Boot.terminal.getCourseById($(this).attr('data-id'));
                            existInYa = Boot.terminal.courseAlreadyPromoted(data.course, ya);
                            if( !existInYa ||
                                session != data.course.session ||
                                (grade >= 1 && grade != data.course._niveau) ||
                               (!(new RegExp(reg,"i").test(data.course.nom)) && !(new RegExp(reg,"i").test(data.course.code)) )
                            ){
                                $(this).addClass('not-super');
                            }
                            else{
                                var seance = 0;
                                $(this).removeClass('not-super').find('.range').each(function(){
                                    var visible = 0;
                                   $(this).find('.hour').each(function(){
                                       $(this)[ya == $(this).attr('data-year') ? 'removeClass'  : 'addClass']('not-super');
                                       if(ya == $(this).attr('data-year')){
                                           visible++;
                                           seance++;
                                       }
                                   });
                                   $(this)[!visible ? 'addClass' : 'removeClass']('not-super');
                                });
                                item.find('.nb_seance').text(seance+" séance"+(seance > 1 ? 's' : ''));
                                total++;
                                visibleTotal++;
                            }
                        });
                    }
                    $(this)[total ? 'removeClass' : 'addClass']('not-super');
                });
                $('.course .course-list .empty-info')[visibleTotal ? 'addClass' : 'removeClass']('not-super');
        };
        var cm = new Card('.course .card-manager');
        cm.onSwitch(function(index){
            $('.course-form')[index == 0 ? 'show' : 'hide']();
            Modules.setSearchVisible(index == 0);
        });
        $('.course')
        .on('click', '.course-item .summary', function(){
            var deploy = !$(this).parent().hasClass('open');
            $('.course-item.open').removeClass('open').find('.see span').text('Plus de détails');
            if(deploy){
                $(this).parent().addClass('open').find('.see span').text('Moins de détails');
            }
        })
        .on('change', '.course-list .sort .input', function(){
            Modules.searchTrigger();
        })
        .on('change', '.faculty .input', function(){
            var val = $(this).val(),
                option = '<option value="">'+($(this).parent().hasClass('sort-list') ? 'Tous les Niveaux' : '')+'</option>'
            if(val.length > 0) {
                var grade = Boot.terminal.getGradesOf(val);
                for (var i in grade) {
                    option += '<option value="' + i + '">' + grade[i].notation + '</option>';
                }
            }
            $(this).parent().parent().find('.grade .input').html(option);
        })
        .on('change', '.schedular [name="grade"]', function(){
            var val = $(this).val(),
                filiere = $(this).parent().parent().find('[name="facname"]').val(),
                list = Boot.terminal.getProfesseur(filiere.length ? filiere : -1, val.length ? val : -1);
            Boot.terminal.setProfOptions(list);
        })
        .on('change', '.schedular [name="facname"]', function(){
            var filiere = $(this).val(),
                val = $(this).parent().parent().find('[name="grade"]').val(),
                list = Boot.terminal.getProfesseur(filiere.length ? filiere : -1, val.length ? val : -1);
            Boot.terminal.setProfOptions(list);
        })
        .on('click', '#schedule', function(){
            var day = set($('#courseForm .days').val(),''),
                begin = $('#courseForm .begin').val(),
                tp = set($('#courseForm .course_type').val(),''),
                oldScheduler = [],
                end = $('#courseForm .end').val();
            AlertBox.set('.course-alert').hide();
            var exist = false;
            if(!day.length){
                $('#courseForm .days').addClass('invalid')
            }
            if(!tp.length){
                $('#courseForm .course_type').addClass('invalid')
            }
            if(begin.length && end.length){
                var dif = sigma(end) - sigma(begin);
                if(dif <= 0 || dif % 60 != 0){
                    $('#courseForm .end').val('').trigger('change');
                    end = '';
                }
                if(day.length && end.length){
                    for(var i in scheduler){
                        if(scheduler[i].day == day &&
                            (
                                (sigma(scheduler[i].begin) < sigma(end) && sigma(scheduler[i].end) > sigma(end)) ||
                                (sigma(scheduler[i].begin) < sigma(begin) && sigma(scheduler[i].end) > sigma(begin))
                            )
                        ){
                            exist = true;
                            break;
                        }
                    }
                }
            }
            if(!day.length || !begin.length || !tp.length || !end.length || exist){
                AlertBox.set('.course-alert')
                    .text(exist ? "Cette intervalle d'heure est en conflit avec l'horaire établi pour ce cours !" : 'Veuillez remplir les champs en alerte !')
                    .type('warn').show();
                return;
            }
            hourId++;
            scheduler.push({
                day: day,
                id: hourId,
                tp: tp == 1,
                begin: begin,
                end: end,
                annee_academique: Modules.data.currentYear.id
            });
            if(currentCourse != null){
                for(var i in currentCourse.horaire){
                    if(Modules.data.currentYear.id != currentCourse.horaire[i].annee_academique) {
                        oldScheduler.push(currentCourse.horaire[i]);
                    }
                }
            }
            oldScheduler = merge(oldScheduler,scheduler);
            Boot.terminal.displayHours(oldScheduler);
        })
        .on('click', '.scheduled-courses item icon', function(){
            var index = $(this).parent().attr('data-index'),
                oldScheduler = [], nScheduler = [];
            if(currentCourse != null){
                for(var i in currentCourse.horaire){
                    if(Modules.data.currentYear.id != currentCourse.horaire[i].annee_academique) {
                        oldScheduler.push(currentCourse.horaire[i]);
                    }
                }
            }
            for(var i in scheduler){
                if(scheduler[i].id != index){
                    nScheduler.push(scheduler[i]);
                }
            }
            scheduler = nScheduler;
            console.log({scheduler,oldScheduler});
            nScheduler = null;
            oldScheduler = merge(oldScheduler,scheduler);
            Boot.terminal.displayHours(oldScheduler);
        })
        .on('click', '#newCourse', function(){
            var data = Form.serialize(form),
                suppleant = data.cr_suppleant;
            delete data.cr_suppleant;
            if(data.cr_fac.length) data.cr_fac = Boot.terminal.getFacById(data.cr_fac).id;
            console.log('Data ',data);
            AlertBox.set('.course-alert').hide();
            if(!Form.isSubmitable(data)){
                form.find('.input').trigger('change');
                AlertBox.text("Veuillez bien remplir les champs").type('warn').show();
                return;
            }
            data.cr_suppleant = suppleant;
            if(scheduler.length == 0){
                form.find('.input').trigger('change');
                form.find('.days')[form.find('.days').val() == undefined ? 'addClass' : 'removeClass']('invalid');
                AlertBox.text("Veuillez spécifier le(s) horaire(s) de cours...").type('warn').show();
                return;
            }
            DialogBox.setWith('ion-load-d animate-spin', 'Requête en cours...', 'none')
            .show();
            data.cr_hours = scheduler;
            data.reqtoken = Modules.data.token;
            data.requid = Modules.data.id;
            if(currentCourse != null && edit){
                data.cr_id = currentCourse.id;
            };
            Fetch.post('./submit', data)
            .then(function(){
                currentCourse = null;
                scheduler = [];
                $('.course-form').trigger('click');
                DialogBox.setWith("ion-android-done",(edit ? "Modification faite": "Enregistrement fait")+" avec succès !", "OK")
                .onClose(function () {
                    History.reach();
                    edit = false;
                })
            })
            .catch(function(e){
                DialogBox.setWith("ion-android-warning", e, "OK");
            });
        })
        .on('click', '.course-form', function(){
            edit = false;
            scheduler = [];
            Boot.terminal.displayHours(scheduler);
            Boot.terminal.setCourseUI(form);
        })
        .on('click', '.course-item .metadata .edit', function(){
            currentCourse = Modules.data.course[$(this).attr('data-index')];
            $('.course .course-form').trigger('click');
            var oldScheduler = [];
            edit = true;
            Boot.terminal.setCourseUI(form, {
                cr_name : currentCourse.nom,
                cr_code : currentCourse.code,
                cr_session : currentCourse.session,
                cr_rate : currentCourse.coefficient,
                cr_principale : currentCourse._titulaire,
                cr_suppleant : currentCourse._suppleant,
                cr_fac : currentCourse._filiere,
                cr_grade : currentCourse._niveau
            });
            for(var i in currentCourse.horaire){
                oldScheduler.push(currentCourse.horaire[i]);
                hourId = currentCourse.horaire[i].id * 1 > hourId ? currentCourse.horaire[i] * 1 : hourId;
                if(Modules.data.currentYear.id == currentCourse.horaire[i].annee_academique) {
                    scheduler.push(currentCourse.horaire[i]);
                }
            }
            form.find('h1').text(Boot.terminal.courseAlreadyPromoted(currentCourse, Modules.data.currentYear.id) ? 'Cours' : 'Mise à jour du cours');
            form.find("[name='cr_grade']").val(currentCourse._niveau).trigger('change');
            Boot.terminal.displayHours(oldScheduler);
        })
        .on('click', '.course-item .metadata .clone', function(){
            var crs = Modules.data.course[$(this).attr('data-index')];
            $('.course .course-form').trigger('click');
            edit = false;
            Boot.terminal.setCourseUI(form, {
                cr_name : crs.nom,
                cr_session : crs.session,
                cr_rate : crs.coefficient,
                cr_grade : crs._niveau
            });
            form.find('#newCourse').text('Enregistrer');
            for(var i in crs.horaire){
                scheduler.push(crs.horaire[i]);
            }
            Boot.terminal.displayHours(scheduler);
            DialogBox.setWith("ion-information", "Vous devriez spécifier le professeur " +
                "titulaire/suppléant, le code du cours, les heures de cours et la faculté/filière en question !", "ok")
                .show();
        })
        .on('click', '.course-item .metadata .delete', function(){
            var data = Modules.data.course[$(this).attr('data-index')],
                message = "En supprimant ce cours, vous supprimez aussi les notes qui lui sont liées (s'il y en a) <br>"+
                    "Avez-vous encore la certitude de supprimer ce cours ?";
            DialogBox.setWith('ion-android-warning', message, 'yesno')
            .show()
            .onClose(function(e,r){
                if(r){
                    DialogBox.setWith('ion-load-b animate-spin', "Requête en cours...", 'none')
                    .show()
                    Fetch.post('./submit',{
                        cr_del_id : data.id,
                        reqtoken: Modules.data.token,
                        requid: Modules.data.id
                    }).then(function(){
                        DialogBox.setWith('ion-android-done', "Suppression réussie !", 'ok')
                        History.reach();
                    }).catch(function(e){
                        DialogBox.setWith('ion-android-warning', e, 'ok')
                    })
                }
            });
        })
        .on('change', '.state .switch', function(){
            var $this = $(this),
                data = Modules.data.course[$(this).attr('data-index')];
            DialogBox
            .setMessage("<icon class='rounded ion-information gray'></icon> L'état de ce cours va être modifié !")
            .setType('YESNO')
            .show()
            .onClose(function(e,r){
                if(!r){
                    $this.trigger('rollback')
                }
                else{
                    console.log()
                    DialogBox.set("<icon class='ion-load-d animate-spin'></icon> Requête en cours...", 'NONE').show();
                    Fetch.post('./submit', {
                        cr_id: data.id,
                        reqtoken: Modules.data.token,
                        requid: Modules.data.id,
                        cr_state: $this.val().toUpperCase()[0]
                    }).then(function(r){
                        DialogBox.set("<icon class='ion-android-done rounded'></icon> Mise en état réussie !", 'OK');
                    }).catch(function(e){
                        $this.trigger('rollback')
                        DialogBox.set("<icon class='ion-android-warning rounded'></icon> "+e, 'OK');
                    })
                }
            })
        })
        .on('change', '.sort-zone [name]', function(){
            var data = Form.serialize($('.schedular .sort-zone')), list = [];
            data.facname = data.facname.length ? data.facname : -1;
            data.grade = data.grade.length ? data.grade : -1;
            data.teacher = data.teacher.length ? data.teacher : -1;
            console.log('[Data]',data);
            if(data.teacher != -1 || (data.facname != -1 && (data.grade != -1 || data.teacher != -1)) ){
                $('.visual .empty-info').addClass('not-super');
                $('.visual .schedular-box').removeClass('not-super');
                list = Boot.terminal.getHoraire(data.facname,data.grade, data.academic, data.teacher, data.session);
            }
            else{
                $('.visual .empty-info').removeClass('not-super');
                $('.visual .schedular-box').addClass('not-super');
            }
            $('.sort-zone .print-schedular')[list.length ? 'removeClass' : 'addClass']('disabled');
            Boot.terminal.setScheduler(list, data);
        })
        .on('click', '.print-schedular', function(){
            if($(this).hasClass('disabled')) return;
            Boot.terminal.print($('.schedular-box')[0],true,900,400);
        })
    },
    "/notes": function(){
        var cm = new Card('.notes .card-manager'),
            currentNoteFilter = null,
            searchCallback = [
                function(e,reg){
                    $('.evaluation .row').each(function(){
                        $(this)[new RegExp(reg, "i").test($(this).attr('data-filter')) || !$(this).attr('data-filter').length ? 'removeClass' : 'addClass']('not-super');
                    });
                },
                function(e,reg){
                    $('.palmares tr.studrow').each(function(){
                        $(this)[new RegExp(reg, "i").test($(this).attr('data-filter')) || !$(this).attr('data-filter').length ? 'removeClass' : 'addClass']('not-super');
                    });
                }
            ];
        cm.onSwitch(function(index){
            Modules.search = searchCallback[index];
        });
        Modules.search = searchCallback[0];
        $('.notes')
        .on('change', '.faculty .input', function(){
            var val = $(this).val(),
                option = '<option value="">'+($(this).parent().hasClass('sort-list') ? 'Tous les Niveaux' : '')+'</option>'
            if(val.length > 0) {
                var grades = Boot.terminal.getGradesOf(val);
                for (var i in grades) {
                    option += '<option value="' + i + '">' + grades[i].notation + '</option>';
                }
            }
            $(this).parent().parent().find('.grade .input').html(option);
        })
        .on('change', '.sort [name="grade"]', function(){
            var val = $(this).val(),
                parent = $(this).parent().parent(),
                filiere = parent.find('[name="facname"]').val(),
                session = parent.find('[name="session"]').val(),
                forEvaluation = true;
            while(parent[0].tagName.toLowerCase() != 'card'){
                parent = parent.parent();
            }
            forEvaluation = parent.hasClass('evaluation');
            var list = Boot.terminal.getCourse(filiere.length ? filiere : -1, val.length ? val : -1, session, Modules.data.currentYear.id);
            Boot.terminal.setCourseOptions(list, forEvaluation);
        })
        .on('change', '.sort [name="facname"]', function(){
            var filiere = $(this).val(),
                parent = $(this).parent().parent(),
                session = parent.find('[name="session"]').val(),
                val = parent.find('[name="grade"]').val(),
                forEvaluation = true;
            while(parent[0].tagName.toLowerCase() != 'card'){
                parent = parent.parent();
            }
            forEvaluation = parent.hasClass('evaluation');
            console.log('[Session]',session);
            var list = Boot.terminal.getCourse(filiere.length ? filiere : -1, val.length ? val : -1, session, Modules.data.currentYear.id);
            Boot.terminal.setCourseOptions(list, forEvaluation);
        })
        .on('change', '.sort [name="session"]', function(){
            var session = $(this).val(),
                parent = $(this).parent().parent(),
                filiere = parent.find('[name="facname"]').val(),
                val = parent.find('[name="grade"]').val(),
                forEvaluation = true;
            while(parent[0].tagName.toLowerCase() != 'card'){
                parent = parent.parent();
            }
            forEvaluation = parent.hasClass('evaluation');
            var list = Boot.terminal.getCourse(filiere.length ? filiere : -1, val.length ? val : -1, session, Modules.data.currentYear.id);
            Boot.terminal.setCourseOptions(list, forEvaluation);
        })
        .on('change', '.sort [name]', function(){
            var parent = $(this).parent(),
                forEvaluation = true;
            while(parent[0].tagName.toLowerCase() != 'card'){
                parent = parent.parent();
            }
            forEvaluation = parent.hasClass('evaluation');
            var data = Form.serialize($('.notes '+(forEvaluation ? '.evaluation' : '.palmares')+' .sort')),
                list = [];
            data.facname = data.facname.length ? data.facname : -1;
            data.grade = data.grade.length ? data.grade : -1;
            data.course = data.course.length ? data.course : -1;
            currentNoteFilter = data;

            if((!forEvaluation || data.course != -1) && data.facname != -1 && data.grade != -1){
                $((forEvaluation ? '.evaluation' : '.palmares')+' .visual .empty-info').addClass('not-super');
                $((forEvaluation ? '.evaluation' : '.palmares')+' .visual .view-list').removeClass('not-super');
                list = forEvaluation ? Boot.terminal.getStudent(data.facname, data.grade) : Boot.terminal.getStudentFromNote(data.facname, data.grade, data.academic);
                console.log({list,data});
            }
            else{
                console.log('[Data]',data);
                $((forEvaluation ? '.evaluation' : '.palmares')+' .visual .empty-info').removeClass('not-super');
                $((forEvaluation ? '.evaluation' : '.palmares')+' .visual .view-list').addClass('not-super');
            }
            if(!forEvaluation){
                $('.notes .palmares .print-notes')[list.length ? 'removeClass' : 'addClass']('disabled');
                Boot.terminal.setPalmares(list, data);
            }
            else{
                Boot.terminal.setNoteEditor(list, data);
            }
        })
        .on('blur', '.evaluation .row .input', function(){
            var note = $(this).val(), filled = 0;
            if(!detect.isNumber(note)){
                $(this).val('').trigger('change').parent().addClass('invalid');
                return;
            }
            note = parseFloat(note);
            if(note > 100){
                $(this).val('').trigger('change').parent().addClass('invalid');
                return;
            }
            $('.evaluation .row .input').each(function(){
                if($(this).val().length){
                    console.log('[fill]')
                    filled++;
                }
            });
            console.log('[Filled]',filled);
            $('.evaluation .submit-notes')[filled ? 'removeClass' : 'addClass']('disabled');
        })
        .on('click', '.evaluation .submit-notes', function(){
            if($(this).hasClass('disabled')) return;
            var crop = {}, k = 0;
            $('.evaluation .row .input').each(function(){
                if($(this).val().length){
                    crop[$(this).attr('data-student')] = $(this).val();
                    k++;
                }
            });
            if(!k) {
                DialogBox.setWith('ion-android-warning', 'Aucune données n\'a été collecté !', 'ok')
                .show();
            }
            DialogBox.setWith('ion-load-d animate-spin', 'Requête en cours...', 'none')
            .show();
            Fetch.post('./submit', {
                reqtoken: Modules.data.token,
                requid: Modules.data.id,
                nt_course : currentNoteFilter.course,
                nt_studnote: crop,
                nt_session: currentNoteFilter.session
            }).then(function(r){
                DialogBox.setWith('ion-android-done green', 'Évaluation effectuée !', 'ok');
                $('.palmares .sort [name]').trigger('change');
            }).catch(function(e){
                DialogBox.setWith('ion-android-warning red', e, 'ok');
                $('.palmares .sort [name]').trigger('change');
            });
        })
        .on('click', '.print-notes', function(){
            if($(this).hasClass('disabled')) return;
            Boot.terminal.print($('.notes .results')[0]);
        })
    },
    "/users": function(){
        var privilegies = [0,6],
            currentUser = null, edit = false,
            form = $('#userForm'),
            personalForm = $('#personalForm'),
            upload = {
                avatar: new ThunderSpeed(),
                myAvatar: new ThunderSpeed()
            },
            cm = new Card('.users .card-manager');
        personalForm.remove();
        personalForm.removeClass('not-super')
        .on('click', '.password-edit-wish', function(){
            personalForm.find('.pswd')[this.checked ? 'removeClass' : 'addClass']('not-super').val('');
        });

        upload.avatar.filterType(['jpeg', 'jpg', 'png'])
        .watch('#picture-file')
        .on('choose', function(e){
            var files = e.files();
            if(files.length){
                var file = files[0];
                if(file.rawsize > 2 * 1024 * 1024){
                    DialogBox.setWith('ion-android-warning', 'Vous devez choisir un fichier image d\'une taille faisant moins de 2 MB', 'ok')
                    .show();
                    return;
                }
                file.getDataURL().then(function(result){
                    $('#studentForm avatar').html('').css('background-image', 'url(' + result + ')')
                });
            }
        })
        .on('progress', function(e){
            DialogBox.setWith("ion-load-d animate-spin","Requête en cours [ " + e.percent + "% ]", 'None');
        });

        upload.myAvatar.filterType(['jpeg', 'jpg', 'png'])
        .watch('#personal_picture')
        .on('choose', function(e){
            var files = e.files();
            if(files.length){
                var file = files[0];
                if(file.rawsize > 2 * 1024 * 1024){
                    DialogBox.setWith('ion-android-warning', 'Vous devez choisir un fichier image d\'une taille faisant moins de 2 MB', 'ok')
                    .show();
                    return;
                }
                file.getDataURL().then(function(result){
                    var lastBG = $('.users .account avatar').css('background-image');
                    $('.users .account avatar').html('').css('background-image', 'url(' + result + ')');
                    DialogBox.setWith('las la-question', 'Uploader cette image comme photo de profil ?', 'yesno')
                    .show()
                    .onClose(function(e,r){
                        if(r){
                            Boot.dispatch.updateInfo({
                                usr_nom : Modules.data.nom,
                                usr_prenom : Modules.data.prenom,
                                usr_pseudo : Modules.data.pseudo,
                                usr_id : Modules.data.id
                            }, upload.myAvatar);
                        }
                        else{
                            $this.parent().find('avatar').html('<icon class="las la-user"></icon>').css('background-image', lastBG);
                        }
                    })
                });
            }
        })
        .on('progress', function(e){
            DialogBox.setWith("ion-load-d animate-spin","Requête en cours [ " + e.percent + "% ]", 'None');
        });

        cm.onSwitch(function(index){
            $('.user-form')[index == 0 ? 'show' : 'hide']();
            Modules.setSearchVisible(index == 0);
            $('.users h1 span').text(index == 0 ? 'Gestion des Utilisateurs' : 'Mon Compte');
        });
        if($('.users .card-tab').length == 0){
            Modules.setSearchVisible(false);
        }
        Modules.search = function(e,reg){
            var total = 0;
            $('.users item').each(function(){
                if(new RegExp(reg, "i").test($(this).attr('data-filter')) || reg.length == 0){
                    $(this).show();
                    total++;
                }
                else{
                    $(this).hide();
                }
            });
            $('.users .empty-info')[!total ? 'show': 'hide']();
        };
        $('.users')
        .on('change', '.modules .input', function(){
            privilegies = [0];
            if($(this).parent().hasClass('head')){
                $(this).parent().find('.more')[this.checked ? 'show' : 'hide']();
                if( $(this).parent().find('.more').hasClass('ion-chevron-up')){
                    $(this).parent().find('.more').trigger('click');
                }
                if(!this.checked){
                    $(this).parent().parent().find('.sub-modules')
                    .find('.input').each(function(){
                        this.checked = false;
                    })
                }
            }
            $(this).parent()[this.checked ? 'addClass' : 'removeClass']('selected');
            $('.users .modules .input').each(function(){
                if(this.checked){
                    privilegies.push($(this).attr('data-value'));
                }
            })
        })
        .on('click', '.modules .more', function(){
            var expand = $(this).hasClass('ion-chevron-down');
            $(this).parent().parent().find('.sub-modules').toggleClass('not-super');
            $(this)[expand ? 'addClass' : 'removeClass']('ion-chevron-up')
                [!expand ? 'addClass' : 'removeClass']('ion-chevron-down');
        })
        .on('click', '#newUser', function(){
            var data = Form.serialize(form),
                psw = data.usr_passcode,
                poste = data.usr_hierarchy;
            delete data.usr_hierarchy;
            delete data.usr_passcode;
            AlertBox.set('.course-alert').hide();
            if(!Form.isSubmitable(data)){
                form.find('.input[name]').trigger('change');
                AlertBox.text("Veuillez bien remplir les champs").type('warn').show();
                return;
            }
            data.usr_hierarchy = poste;
            data.usr_passcode = psw;
            data.usr_access = JSON.stringify(privilegies);
            DialogBox.setWith("ion-load-d animate-spin","Requête en cours..", 'None')
            .show();
            if(currentUser != null){
                data.usr_id = currentUser.id;
            }
            upload.avatar.params(extend(data,{
                url: './submit',
                fileIndexName: 'usr_upl_avatar',
                uploadedFileIndexName: 'usr_avatar',
                reqtoken: Modules.data.token,
                requid: Modules.data.id
            })).start().then(function(r){
                History.reach();
                currentUser = null;
                privilegies = [0];
                Modules.updateData(r.data);
                DialogBox.setWith("ion-android-done green", edit ? "Utilisateur modifié !" : "Utilisateur ajouté !", 'Ok')
                edit = false;
            }).catch(function(e){
                DialogBox.setWith("ion-android-warning",e, 'ok');
            });
        })
        .on('click', '.user-form', function(){
            currentUser = null;
            privilegies = [0];
            edit = false;
            Boot.terminal.setUserUI(form,currentUser);
        })
        .on('click', '.user-item .edit', function(){
            $('.users .user-form').trigger('click');
            currentUser = Modules.data.users[$(this).attr('data-index')];
            Boot.terminal.setUserUI(form,currentUser);
            privilegies = currentUser.privileges.split(',');
            console.log('[PRV]',privilegies);
            edit = true;
        })
        .on('click', '.user-item .delete', function(){
            var data = Modules.data.users[$(this).attr('data-index')],
                message = "Avez-vous encore la certitude de supprimer cet utilisateur ?";
            DialogBox.setWith('ion-android-warning', message, 'yesno')
            .show()
            .onClose(function(e,r){
                if(r){
                    DialogBox.setWith('ion-load-d animate-spin', "Requête en cours...", 'none')
                    .show()
                    Fetch.post('./submit',{
                        usr_del_id : data.id,
                        reqtoken: Modules.data.token,
                        requid: Modules.data.id
                    }).then(function(){
                        DialogBox.setWith('ion-android-done', "Suppression réussie !", 'ok')
                        History.reach();
                    }).catch(function(e){
                        DialogBox.setWith('ion-android-warning', e, 'ok')
                    })
                }
            });
        })
        .on('change', '.user-item .state', function(){
                var $this = $(this),
                    data = Modules.data.users[$(this).attr('data-index')];
                console.log('[Val]',$this.val());
                DialogBox
                .setMessage("<icon class='rounded ion-information gray'></icon> L'état de cet utilisateur va être modifié !")
                .setType('YESNO')
                .show()
                .onClose(function(e,r){
                    if(!r){
                        $this.trigger('rollback');
                    }
                    else{
                        DialogBox.set("<icon class='ion-load-d animate-spin'></icon> Requête en cours...", 'NONE').show();
                        Fetch.post('./submit', {
                            usr_id: data.id,
                            reqtoken: Modules.data.token,
                            requid: Modules.data.id,
                            usr_state: $this.val() ? "actif" : "false"
                        }).then(function(r){
                            DialogBox.set("<icon class='ion-android-done rounded'></icon> Mise en état réussie !", 'OK');
                        }).catch(function(e){
                            $this.trigger('rollback')
                            DialogBox.set("<icon class='ion-android-warning rounded'></icon> "+e, 'OK');
                        })
                    }
                })
        })
        .on('click', '.users .avatar-chooser', function(){
            $(this).parent().find('input[type="file"]').click();
        })
        .on('click', '#editMyself', function(){
            Form.reset(personalForm, {
                usr_nom : Modules.data.nom,
                usr_prenom : Modules.data.prenom,
                usr_pseudo : Modules.data.pseudo
            });
            FormPopup.title("Infos personnelles").html(personalForm).show().onAction(function(submit,data){
                // console.log('[E]',data);
                if(submit){
                    var passcode = [data.usr_passcode, data.usr_new_passcode];
                    delete data.usr_passcode;
                    delete data.usr_new_passcode;
                    if(Form.isSubmitable(data)){
                        if((passcode[0].length && !passcode[1].length) || (!passcode[0].length && passcode[1].length)){
                            DialogBox.set('ion-android-warning red', "Veuillez remplir correctement les champs !", 'ok')
                                .show();
                            return;
                        }
                        data.usr_passcode = passcode[0];
                        data.usr_new_passcode = passcode[1];
                        Boot.dispatch.updateInfo(data, upload.myAvatar);
                    }
                    else{
                        DialogBox.set('ion-android-warning red', "Veuillez remplir correctement les champs !", 'ok')
                            .show();
                    }
                }
            })
            personalForm.find('[name]').trigger('change');
        })
    },
    "/": function(){
        var canvasContext = {
            timeline : $('#timeline')[0].getContext('2d'),
            promotion : $('#promotion')[0].getContext('2d')
        },
        charts = {},
        data = Modules.data.dashboard,
        datasets = [], colors = {
            background : [
                'rgba(255, 99, 132, 0.2)',
                'rgba(54, 162, 235, 0.2)',
                'rgba(255, 206, 86, 0.2)',
                'rgba(75, 192, 192, 0.2)'
            ],
            border: [
                'rgba(255, 99, 132, 1)',
                'rgba(54, 162, 235, 1)',
                'rgba(255, 206, 86, 1)',
                'rgba(75, 192, 192, 1)'
            ]
        };
        Modules.setSearchVisible(false);
        //timeline
        var tmp,k=0, names = ['étudiants', 'professeurs', 'cours', 'filières'],
            index = [];
        for(var i in data.etudiantTimeLine){
            index.push(i);
        }
        index.sort();
        datasets.push({labels: [], datasets: []});
        for(var i in index){
            i = index[i];
            datasets[0].labels.push(i);
            tmp = [data.etudiantTimeLine[i], data.profTimeLine[i], data.courseTimeLine[i], data.filiereTimeLine[i]];
            if(!datasets[0].datasets.length) {
                for(var k = 0 ; k < tmp.length; k++){
                    datasets[0].datasets.push({
                        label: names[k],
                        data: [tmp[k]],
                        borderColor: colors.border[k % 5],
                        backgroundColor: colors.background[(k + 1) % 5],
                        tension: 0.8,
                        borderWidth: 1,
                        fill: true
                    });
                }
            }
            else{
                for(var k = 0 ; k < tmp.length; k++){
                    datasets[0].datasets[k].data.push(tmp[k])
                }
            }
        }

        charts.timeline = new Chart(canvasContext.timeline, {
            type: 'line',
            data: datasets[0],
            options: {
                indexAxis: 'x',
                scales: {
                    x: {
                        beginAtZero: false
                    }
                }
            }
        });
        var percent = Math.round(data.lastYearPromotedStudents / (data.lastYearPromotedStudents + data.lastYearStackStudents) * 10000) / 100;
        charts.promotion = new Chart(canvasContext.promotion, {
            type: 'doughnut',
            data:{
                labels: ['réussite en %', 'échec en %'],
                datasets: [{
                    data: [percent,100-percent],
                    backgroundColor: [colors.border[3], colors.border[0]],
                    hoverOffset: 8,
                    cutout: '60%'
                }]
            }
        })
    }
};