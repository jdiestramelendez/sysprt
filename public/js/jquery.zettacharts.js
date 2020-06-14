(function ( $ ) {
$.fn.zettaCharts = function(options) {
    var target = this;

    switch(options.type){
        case "bar":

        break;
        case "barHorizontal":
            var chartHolder = "<div class='zettaChart' id=''>";
                chartHolder+= "<ul></ul></div>";

            this.empty().append(chartHolder);

            var chart = $('.zettaChart');
            var dataIndex = 0;
            var chartLine = '';
            var lineH = this.height()/options.data.length;
            var sortValues = options.data;
            var larger = sortValues[0];

            $.each(options.data,function(k,v){
                var legend = options.labels[dataIndex];
                var label = v;

                moment.locale('pt');
                var barID = 'zettaChart_bar_'+dataIndex;

                var dateDrop = moment(v*1000).format('LTS');

                var colorIndex = Number(dataIndex)+1;

                chartLine = "<li style='height:"+lineH+"px' id='"+barID+"' title='"+options.labels[dataIndex]+"'><div class='zettaChart_verticalLegends'>"+options.labels[dataIndex]+"</div>";
                chartLine+= "<div class='zettaChart_barContainer'>";
                chartLine+= "<div class='zettaChart_barHolder'>";
                chartLine+= "<span class='zettaChart_bar dark_chart_color_"+colorIndex+"'>";
                chartLine+= "<label>"+dateDrop+"</label>";
                chartLine+= "</span></div></div></li>";

                chart.find('ul').append(chartLine);

                var barMax = $('.zettaChart_barHolder').width();
                var barW = (barMax/larger)*v;

                $('#zettaChart_bar_'+dataIndex).find('.zettaChart_bar').stop().delay(200*dataIndex).animate({
                    width: barW
                },500,'easeOutCubic',function(){

                });
              //  var labelW = $('.zettaChart_bar').find('label').width();
                var labelW = 45;

                if(barW<labelW){
                    var margin =  -(barW+4);

                    $('#zettaChart_bar_'+dataIndex).find(".zettaChart_bar>label").css({
                        left: 'auto',
                        right: margin
                    });
                }

                dataIndex++;
            });
        break;
        case 'ganttLines':
            var ganttLinesSetting = {
                timeRange: 24,
                totalDays: 1,
                labels: [],
                data: [],
                totals: Number,
            };

            var settings = $.extend(true, {}, ganttLinesSetting, options );

            ganttLines = {
                elements: {
                    controller: '',
                    dragBar: '',
                    dragBarHolder: '',
                    timeTrack: ''
                },
                params:{
                    isDraggable: Boolean
                },
                run: function (gl){
                    var totalDays = settings.totalDays;
                    var timeRange = settings.timeRange*totalDays;

                    var idSequencer = new Date();
                        idSequencer = idSequencer.getTime();

                    var inicio = moment(settings.data[0][0].dtInicio).startOf('day').format("DD/MM/YYYY");
                    var fim =  moment(settings.data[0][0].dtFim).endOf('day').format("DD/MM/YYYY");

                    var ganttLinesChart = "<div class='zettaChart zettaChart_ganttLines' id='zettaChart_ganttLines_"+idSequencer+"'>";
                        ganttLinesChart = "<div class='zettaChart_ganttLines_titleBar'><h2 class='color_darkGrey'>de <b>"+inicio+" - <span class='zettaChart_ganttLines_rangeStart'></span></b> até <b>"+fim+" - <span class='zettaChart_ganttLines_rangeEnd'></span></b></h2></div>";
                        ganttLinesChart+= "<div class='zettaChart_ganttLines_content'>";
                        ganttLinesChart+= "<div class='zettaChart_ganttLines_labelHolder'>";
                        ganttLinesChart+= "<div class='zettaChart_ganttLines_line zettaChart_ganttLines_header zettaChart_ganttLines_label'></div></div>";
                        ganttLinesChart+= "<div class='zettaChart_ganttLines_barsHolder'>";
                        ganttLinesChart+= "<div class='zettaChart_ganttLines_barsSuperController'>";
                        ganttLinesChart+= "<div class='zettaChart_ganttLines_line zettaChart_ganttLines_header zettaChart_ganttLines_bars'>";
                        ganttLinesChart+= "<div class='zettaChart_ganttLines_gridHolder zettaChart_ganttLines_timeTrack'></div></div></div></div>";
                        ganttLinesChart+= "<div class='zettaChart_ganttLines_totalHolder'>";
                        ganttLinesChart+= "<div class='zettaChart_ganttLines_line zettaChart_ganttLines_header zettaChart_ganttLines_total'></div></div>";
                        ganttLinesChart+= "</div></div>";

                    gl.empty().append(ganttLinesChart);

                    ganttLines.elements.controller = gl.find('.zettaChart_ganttLines_barsHolder');
                    ganttLines.elements.timeTrack = gl.find('.zettaChart_ganttLines_timeTrack');
                    ganttLines.elements.dragBar = gl.find('.zettaChart_ganttLines_barsSuperController');
                    ganttLines.elements.dragBarHolder = gl.find('.zettaChart_ganttLines_barsHolder');

                    var timeTrack = ganttLines.elements.timeTrack;
                    var dragBar = ganttLines.elements.dragBar;
                    var dragBarHolder = ganttLines.elements.dragBarHolder;

                    var hourSplit = 7;
                    var hourSplitLine = "<span></span>";
                    var getPanelW = dragBarHolder.width();
                    //console.log('getPanelW: ',getPanelW);
                    var timeDrop = 0;

                    for(i=0;i<timeRange;i++){
                        var dropTimeLabel = (timeDrop<10 ? '0'+timeDrop : timeDrop);

                        var timeTrackItem = "<div><label>"+dropTimeLabel+"</label><div class='zettaChart_ganttLines_timeSlot'>";
                        var minTrackItem = "<label class='zettaChart_ganttLines_floatingTimeTrack'>"+Number(dropTimeLabel+1)+"</label>";
                        for(s=0;s<hourSplit;s++){
                            timeTrackItem += hourSplitLine;
                        }
                        if(i==timeRange-1 && totalDays==1){
                            // timeTrackItem+= "</div>";
                            // timeTrackItem+=minTrackItem;
                            // timeTrackItem+= "</div>";
                            timeTrackItem+= "</div></div>";
                        }else{
                            timeTrackItem+= "</div></div>";
                        }
                        timeTrack.append(timeTrackItem);
                        timeDrop = (timeDrop==(settings.timeRange-1) ? 0 : timeDrop+=1);
                    }

                    var labelHolder = gl.find('.zettaChart_ganttLines_labelHolder');
                    var totalHolder = gl.find('.zettaChart_ganttLines_totalHolder');
                    var barHolder = gl.find('.zettaChart_ganttLines_barsSuperController');

                    var barItem = "<div class='zettaChart_ganttLines_line zettaChart_ganttLines_bars'>";
                        barItem+= "<div class='zettaChart_ganttLines_barsController'>";
                        barItem+= "<div class='zettaChart_ganttLines_barWrap'></div></div>";
                        barItem+= "<div class='zettaChart_ganttLines_gridHolder'></div></div>";

                    $.each(settings.labels,function(k,v){
                        var labelItem = "<div class='zettaChart_ganttLines_line zettaChart_ganttLines_label'>";
                            labelItem+= "<label>"+v+"</label></div>";

                        var totalItem = "<div class='zettaChart_ganttLines_line zettaChart_ganttLines_total'>";
                            totalItem+= "<p>"+settings.totals[k]+"</p></div>";

                        labelHolder.append(labelItem);
                        totalHolder.append(totalItem);
                        barHolder.append(barItem);
                    });

                    var gridholder = gl.find('.zettaChart_ganttLines_bars .zettaChart_ganttLines_gridHolder');

                    gridholder.each(function(){
                        var notHeader = ($(this).hasClass('zettaChart_ganttLines_timeTrack') ? true : false);

                        if(!notHeader){
                            for(s=0;s<timeRange;s++){
                                var gridItem = (s==24 || s==48 ? "<span><span class='zettaChart_ganttLines_daySep'></span></span>" : "<span></span>");
                                $(this).append(gridItem);
                            }
                        }
                    });

                    var timeTrackSlot = timeTrack.find("div");
                    var minSlotW = 31;
                    var totalW = minSlotW*timeRange;


                    ganttLines.params.isDraggable = false;

                    if(timeTrackSlot.width()<minSlotW){
                        dragBar.width(totalW);
                        labelHolder.addClass('has-drag');
                        totalHolder.addClass('has-drag');
                        ganttLines.draggable.on(dragBar);
                        ganttLines.params.isDraggable = true;
                    }
                    var isDraggable = ganttLines.params.isDraggable;

                    var barWrap = gl.find('.zettaChart_ganttLines_barWrap');
                    var barWrapIndex = 0;
                    var firstBarPos;

                    $.each(settings.data,function(k,v){
                        if(v.length!=0){
                            minDate = (v[0].label=='Jornada de Trabalho' ? moment(v[0].dtInicio) : minDate);
                        }
                        if(v.length!=0){
                            $.each(v,function(k,v){
                                moment.locale('pt');

                                var ini = moment(v.dtInicio);
                                    ini = ini.unix();
                                var fim = moment(v.dtFim);
                                    fim = fim.unix();

                                var getRange = fim-ini;
                                var totalW = barWrap.width();
                                var hourW = totalW/timeRange;
                                var minuteW = hourW/60;
                                var secondW = minuteW/60;
                                var barW = secondW*getRange;
                                var dayStart = moment(minDate).startOf('day');
                                    dayStart = dayStart.unix();

                                var dif = ini-dayStart;
                                var barPos = dif*secondW;

                                    firstBarPos = (k==0 ? barPos : firstBarPos);

                                if(!isDraggable){
                                    if(window.innerHeight>864){
                                        barPos-=5;
                                        barW+=3;
                                    }else{
                                        barPos-=1;
                                    }
                                }
                                var item = "<span style='width: "+barW+"px; left: "+barPos+"px' class='"+v.css_Class+"'>";
                                    item+= "<div class='zettaChart_ganttLines_tooltip "+v.css_Class+"_tooltip'>"+v.label+"<br>"+moment(v.dtInicio).format('DD/MM/YYYY-HH:mm:ss')+" <i class='fa fa-angle-right'></i> "+moment(v.dtFim).format('DD/MM/YYYY-HH:mm:ss')+"</div></span>";

                                barWrap.eq(barWrapIndex).append(item);
                            });
                          }
                        barWrapIndex++;
                    });

                    var params = ganttLines.draggable.params(dragBar);
                    var rightContainment = params.rightContainment;
                    var desloc = labelHolder.outerWidth();
                    var margin = 50;
                    var loadPos = desloc-firstBarPos+margin;

                    loadPos = (loadPos<rightContainment ? rightContainment+1 : loadPos);

                    dragBar.css('left',loadPos);
                    if(isDraggable){
                        ganttLines.draggable.trackTimeRange(dragBar.position().left);
                    }else{
                        ganttLines.draggable.addEndOfDay();
                    }
                    if(timeTrackSlot.width()>=minSlotW){
                        dragBar.width(dragBarHolder.width()-1);
                    }
                    //ganttLines.draggable.trackTimeRange(dragBar.position().left);
                },
                draggable:{
                    params: function (dragBar){
                        var barHolder = $('.zettaChart_ganttLines_barsHolder');
                        var rightContainment = barHolder.width()-dragBar.width();

                        params = {
                            leftContainment:0,
                            rightContainment: rightContainment,
                            desloc: 200
                        }
                        return params;
                    },
                    on: function (dragBar){
                        var params = ganttLines.draggable.params(dragBar);
                        var rightContainment = params.rightContainment;

                        dragBar.addClass('zettaChart_ganttLines_grab').css('left',-1).draggable({
                            axis: "x",
                            //containment: [133, 0, 1000, 0],
                            start: function (e) {
                              var desloc = params.desloc;

                              $(document).on('mousemove', dragBar,function(){
                                  //console.log('dragBar.position().left: ',dragBar.position().left);
                                  ganttLines.draggable.trackTimeRange(dragBar.position().left);
                                  if(dragBar.position().left>desloc){
                                      ganttLines.draggable.stop(dragBar,'left');
                                  }
                                  if(dragBar.position().left<(rightContainment-desloc)){
                                      ganttLines.draggable.stop(dragBar,'right');
                                  }
                              });
                            },
                            stop: function () {
                                if(dragBar.position().left>0){
                                    ganttLines.draggable.stop(dragBar,'left');
                                }
                                if(dragBar.position().left<rightContainment){
                                    ganttLines.draggable.stop(dragBar,'right');
                                }
                            }
                        });
                    },
                    stop: function (dragBar,side){
                        var params = ganttLines.draggable.params(dragBar);
                        var finish = function(){
                            ganttLines.draggable.trackTimeRange(dragBar.position().left)
                        };
                        switch(side){
                            case 'left':
                                dragBar.animate({
                                    'left': 0
                                },300,'easeOutCubic',finish);
                            break;
                            case 'right':
                                dragBar.stop().animate({
                                    'left': params.rightContainment
                                },300,'easeOutCubic',finish);
                            break;
                        }

                        $(document).unbind('mousemove');
                    },
                    trackTimeRange: function (x){
                        var dragBar = ganttLines.elements.dragBar;
                        var controller = ganttLines.elements.controller;
                        var timeTrack = ganttLines.elements.timeTrack;

                        var controllerW = controller.outerWidth();
                        var timeTrackW = timeTrack.find('div').outerWidth();
                        var dragW = dragBar.outerWidth();

                        var range = (controllerW/timeTrackW);
                        var timeDesloc = x/timeTrackW;
                            timeDesloc = (timeDesloc<0 ? -timeDesloc : 0);
                            timeDesloc = timeDesloc.toFixed(2).split('.');

                        var hour = timeDesloc[0];
                            hour = (hour<0 ? 0 : hour);
                            hour = (hour>23 ? hour-24 : hour);
                            hour = (hour<10 ? '0'+hour : hour);

                        var min = parseInt((timeDesloc[1]/100)*60);
                            min = (min<0 ? 0 : min);
                            min = (min<10 ? '0'+min : min);

                        var startTimeTrack = hour+":"+min;

                        var endTimeTrack = moment(startTimeTrack,'HH:mm');
                            endTimeTrack = endTimeTrack.add(range,'hours');

                        var endOfDay = moment(endTimeTrack).endOf('day');
                        var isAfter = moment(endTimeTrack).isAfter(endOfDay);

                        endTimeTrack = endTimeTrack.format('HH:mm');
                        endTimeTrack = (endTimeTrack.toString()=="00:00" ? '23:59' : endTimeTrack);

                        $('.zettaChart_ganttLines_rangeStart').text(startTimeTrack);
                        $('.zettaChart_ganttLines_rangeEnd').text(endTimeTrack);
                    },
                    addEndOfDay: function (){
                        $('.zettaChart_ganttLines_rangeStart').text('00:00');
                        $('.zettaChart_ganttLines_rangeEnd').text('23:59');
                    }
                }
            }

            ganttLines.run(this);
        break;
        case 'ganttTracker':
            var ganttTrackerSettings = {
                type: 'ganttTracker',
                totalDays: 3,
                timeRange: 12,
                minRange: 0,
                minTrackSelector:{
                    hasTrack: false,
                    selector: ''
                },
                options:{
                    mobileSize: '768px',
                    minRef: {
                        2: 5,
                        4: 10,
                        6: 15,
                        8: 20,
                        12: 30
                    },
                    mobileMinRef: {
                        2: 5,
                        4: 10,
                        6: 15,
                        8: 20,
                        12: 30
                    }
                }
            };
            var settings = $.extend(true, {}, ganttTrackerSettings, options );

            // console.log('settings: ',settings);
            var ganttTracker = {
                  elements: {
                      componentHeader: target.find('.componentHeader'),
                      contentLine: target.find(".contentLine"),
                      timePack: target.find('.timePack'),
                      componentHeader: target.find('.componentHeader')
                  },
                  params: {
                      padd: 0,
                      pgPad: 30,
                      pgW: window.innerWidth,
                      nb: 3,
                      mg: 1,
                      minTimePackW: 80,
                      sep: 1,
                      currentRange: 12,
                      containLeft: 0,
                      containRight: 0
                  },
                  run: function (){
                      ganttTracker.functions.timelineControl(settings.timeRange);
                      ganttTracker.events();
                  },
                  functions:{
                      timelineControl: function (timerange){
                          ganttTracker.functions.timeHeader(timerange);

                          var fullW = ganttTracker.elements.componentHeader.width();
                          ganttTracker.elements.contentLine.width(fullW);

                          if (target.attr('data-now') == "") {
                              var currentdate = new Date();
                              currentdate = currentdate.getHours() + ":" + currentdate.getMinutes() + ":" + currentdate.getSeconds();
                              target.attr('data-now', currentdate);
                          }
                          if (target.attr('data-now') != "" || ganttTracker.elements.componentMainHolder.attr('data-now') != undefined) {
                              var m = 60;
                              var h = 24;
                              var totalW = target.find('.componentHeader').width();
                              var totalHours = settings.totalDays*h;

                              var wPerSec = ((totalW/totalHours)/m)/m;

                              var timenow = target.attr('data-now');
                                  timenow = moment(timenow,"HH:mm:ss").add(parseInt(settings.totalDays/2), 'days').subtract(8,'hours');

                              var dateStart = target.find(".timeline").eq(target.find(".timeline").length-1).attr('data-start');
                                  dateStart = moment(dateStart,"DD/MM/YYYY HH:mm:ss");
                              var dateDayStart =  moment(dateStart,"DD/MM/YYYY").startOf('day');
                              var floatSlot = timenow.diff(dateDayStart, 'seconds');

                              var fullW = ganttTracker.elements.componentHeader.width();
                              var contain_left = -((fullW - ganttTracker.params.pgW) + ganttTracker.params.padd + ((window.innerWidth - target.width()) / 2));
                              var contain_right = ((window.innerWidth - target.width()) / 2) + ganttTracker.params.padd;

                              ganttTracker.params.containLeft = contain_left;
                              ganttTracker.params.containRight = contain_right;

                              var controllerLeft = floatSlot*wPerSec;
                                  controllerLeft = (controllerLeft<contain_left ? contain_left : controllerLeft);
                                  controllerLeft = (controllerLeft>contain_right ? contain_right : controllerLeft);

                              // console.log('controllerLeft: ',controllerLeft);
                              target.css('left', -controllerLeft);
                          }


                          // target.css('left', offset);

                          var minRef = (window.innerWidth<settings.options.mobileSize ? settings.options.mobileMinRef : settings.options.minRef);
                          var getMinScale = Number(minRef[eval(timerange)]);

                          ganttTracker.functions.minuteRuler(getMinScale);
                          ganttTracker.functions.timeline();

                          ganttTracker.draggable.on();
                      },
                      timeHeader: function (timerange){
                          var timeSep = "<li class='timeSep'></li>";
                          var day = 24;
                          var totalHours = settings.totalDays*day;
                          ganttTracker.elements.componentHeader.empty();

                          for(i=0;i<totalHours;i++){
                            var dayLoop = parseInt(i/day);

                            var timeStamp = (i>=day ? i-(day*dayLoop) : i);
                                timeStamp = (timeStamp<10 ? '0'+timeStamp: timeStamp);
                                timeStamp = timeStamp+":00:00";

                            var timePack = "<li class='timePack'>";
                                timePack+= "<label class='noselect grab'>"+timeStamp+"</label></li>";

                            if(i==0) ganttTracker.elements.componentHeader.append(timePack);
                            else ganttTracker.elements.componentHeader.append(timeSep+timePack);
                          }

                          var pageW = target.parent().parent().width();

                          var packW = Math.ceil(pageW / timerange);
                              packW = (packW < ganttTracker.params.minTimePackW ? ganttTracker.params.minTimePackW : packW - ganttTracker.params.sep);

                          target.find('.timePack').width(packW);
                      },
                      timeline: function (){
                          var m = 60;
                          var h = 24;
                          var totalW = target.find('.componentHeader').width();
                          var totalHours = settings.totalDays*h;

                          target.find(".timeline").css('opacity',0).each(function () {
                              var i = $(this).index();
                              var s = $(this).attr('data-start');
                              var e = $(this).attr('data-end');

                              moment.locale('pt');

                              var dateStart = moment(s,"DD/MM/YYYY HH:mm:ss");
                              var dateEnd = moment(e,"DD/MM/YYYY HH:mm:ss");
                              var dateDayStart =  moment(s,"DD/MM/YYYY").startOf('day');
                              var timeSlot = dateEnd.diff(dateStart, 'seconds');
                              var iniSlot = dateStart.diff(dateDayStart, 'seconds');

                              var wPerSec = ((totalW/totalHours)/m)/m;
                              var finalW = timeSlot * wPerSec;

                              if(i==0){
                                  var iniLeft = iniSlot*wPerSec;
                                  $(this).css('margin-left', iniLeft);
                              }

                              $(this).css({
                                'opacity': 1,
                                'width': finalW
                              });
                          });
                      },
                      minuteRuler: function (minRange){
                          var m = 60;
                          var totalSpace = target.find('.timePack').width();
                          var minSpace = totalSpace / m;
                          var minRangeSize = (minSpace * minRange);
                          var slotCount = m / minRange;

                          target.find('.minSepHolder').remove();

                          target.find('.timePack').each(function () {
                              $(this).append("<div class='minSepHolder'></div>");
                              for (i = 0; i < slotCount; i++) {
                                  var dropMin = (i > 0 ? ':' + minRange * i : "");
                                  $(this).find('.minSepHolder').append("<div class='minSep' style='width:" + minRangeSize + "px'><span class='noselect'>" + dropMin + "</span></div>");
                              }
                          });
                      }
                  },
                  tooltip:{
                      params: {
                          pad: 4
                      },
                      element: function (t){
                          var tooltip = "<span class='tlHeaderTooltip'>"+t+"</span>";
                          return tooltip;
                      },
                      controller: function (item,e){
                          if (e.type == 'mouseenter') {
                              var tip = item.find('label').text();
                              var id = item.attr('track-id');

                              item.append(dropTooltip(tip));

                              var offset = item.offset();
                              var tipX = (e.pageX - offset.left) + ganttTracker.tooltip.params.pad;
                              //console.log('tipX:',tipX);

                              $('.tlHeaderTooltip').css({
                                  'left': tipX
                              });
                          }
                          if (e.type == 'mouseleave') {
                              ganttTracker.tooltip.remove();
                          }
                      },
                      remove: function (){
                          $('.tlHeaderTooltip').remove();
                      }
                  },
                  draggable: {
                      on: function (){
                          var contain_left = ganttTracker.params.containLeft;
                          var contain_right = ganttTracker.params.containRight;

                          target.draggable({
                              axis: "x",
                              containment: [contain_left, 0, contain_right, 0],
                              start: function (e) {
                                  var dw = target.width();
                                  //console.log('dw:', dw);
                                  $(document).on('mousemove', function (e) {
                                      var cl = (window.innerWidth - dw) / 2;
                                      var dif = 40;
                                      var cr = (cl + dw) - dif;
                                      var mousex = e.clientX;
                                      if (mousex >= cr) $(document).trigger('mouseup');
                                  });
                              },
                              stop: function () {
                                  $(document).unbind('mousemove');
                              }
                          });
                      },
                      destroy: function (){

                      }
                  },
                  events: function (){
                      if(settings.minTrackSelector.hasTrack){
                          $(document).on('change', settings.minTrackSelector.selector, function () {
                              var selected = $(this).find(":selected");
                              var val = (selected.length>1 ? selected.eq(selected.length-1).val() : selected.val());

                              ganttTracker.params.currentRange = val;
                              ganttTracker.functions.timelineControl(val);
                          });
                      }

                      $(document).on('mouseenter mouseleave', '.contentLineGrouper .timeline', function (e) {
                          ganttTracker.tooltip.controller($(this),e);
                      });
                      $(window).on('resize',function(){
                          ganttTracker.functions.timelineControl(ganttTracker.params.currentRange);
                      });
                  }
            }

        ganttTracker.run();
        break;
    }

    return this;
}
}( jQuery ));
