$(document).ready(function(){
  window.events = {
    options: {
      eventsLoaded: false,
      resizeTimeout: null,
      data: null,
      cache: null,
      eventList: [],
      range: 12,
      currentDate: null,
      eventListConfig: null,
      eventNames: {
        // 101: "Excesso de velocidade",
        // 103: "Limite rpm",
        // 105: "Marcha lenta excessiva",
        // 107: "Marcha lenta",
        // 109: "Excesso de velocidade na chuva",
        // 111: "Alta rotação 2",
        // 113: "Excesso de rotação",
        // 115: "Aceleração extrema",
        // 116: "Freada extrema",
        // 117: "Freada brusca",
        // 118: "Aceleração brusca",
        // 119: "Excesso de velocidade em curva",
        // 121: "Excesso de velocidade controlada",
        // 123: "Alta rotação 1",
        // 125: "Pedal acelerador excessivo"
      }
    },
    dashboard: {
      start: () => {
        events.dashboard.list.get()

        $( ".timelineDateSet" ).datepicker({
          dateFormat: 'dd/mm/yy',
          dayNames: ['Domingo','Segunda','Terça','Quarta','Quinta','Sexta','Sábado'],
          dayNamesMin: ['D','S','T','Q','Q','S','S','D'],
          dayNamesShort: ['Dom','Seg','Ter','Qua','Qui','Sex','Sáb','Dom'],
          monthNames: ['Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'],
          monthNamesShort: ['Jan','Fev','Mar','Abr','Mai','Jun','Jul','Ago','Set','Out','Nov','Dez'],
          minDate: new Date("2018-11-22"),
          maxDate: "+0d",
          nextText: '',
          prevText: ''
        });
      },
      list: {
        get: () => {
          const self = events.dashboard.list
          $(".eventFilterSelect a").addClass("off")

          axios.get('geteventslibrary')
          .then(function (response) {
            // handle success3
            self.drop(response.data)
            console.log("response: ", response.data);
          })
          .catch(function (error) {
            // handle error
            console.log("error: ", error);
          })
          .then(function () {
            $(".eventFilterSelect a").removeClass("off")
            // always executed
          })
        },
        drop: (data) =>{
          const self = events.dashboard.list
          // console.log("data: ",data)

          for(var i = 0; i < data.length; i++){
            const id = Number(data[i].EventId)
            const desc = data[i].Description
            const status = data[i].status

            events.options.eventNames[i] = {
              id: id,
              desc: desc,
              status: status
            }
          }

          events.dashboard.gantt.run()
          self.populate()
          events.dashboard.resize()
        },
        populate: () => {
          const content = events.options.eventNames
          const eventListController = $(".eventList ul")
                eventListController.empty()
          const eventFilterList = $(".eventFilterList")
                eventFilterList.empty()

          $.each(content,function(k,v){
            const checked = (eval(v.status) ? "checked" : "")
            const off = (eval(v.status) ? "" : "off")

            const item = "<li data-id='" + v.id + "' class='" + off + "'>" +
                         "<p class='noselect'>" + v.desc + "</p>"+
                         "<div></div></li>"

            const selectItem = "<li data-id='" + v.id + "' class='drag'>" +
                               "<i class='fas fa-ellipsis-v'></i> " +
                               "<span>" + v.desc + "</span>" +
                               "<div class='checkboxHolder'><input type='checkbox' class='iCheck " + checked + "' id='" + k + "' " + checked + "></div>"  +
                               "</li>"
            
            eventListController.append(item)
            eventFilterList.append(selectItem)
          })

          eventFilterList.sortable()
          eventFilterList.disableSelection()

          blustock.form.checkbox.run()
        },
        record: (list) => {
          var ids = []
          var states = []
          const self = events.dashboard.list

          for(var i = 0; i < list.length; i++){
            const item = $(list[i])
            const id = item.attr("data-id")
            const state = item.find("input[type='checkbox']").prop("checked")
            const isPlaceholder = item.hasClass("ui-sortable-placeholder")
            
            if(!isPlaceholder) {
              ids.push(id)
              states.push(state)
            }
          }

          const data = {
            event_id: ids.toString(),
            status: states.toString()
          }

          events.options.eventListConfig = data
          // self.order(data)
          self.showButton(true)
        },
        save: () => {
          const self = events.dashboard.list
          const data = events.options.eventListConfig
          const bt = $(".saveEventListConfig")
          
          bt.addClass("off")
          bt.find(".btnSpinner").addClass("on")

          // console.log("data: ",data)

          axios.post('/events_order', data)
            .then(function (response) {
              // console.log("save response: ", response)
              self.showButton(false)
              self.get()
              blustock.template.spinner.on()
            })
            .catch(function (error) {
              // handle error
              console.log("error: ", error)
            })
            .then(function () {
              bt.removeClass("off")
              bt.find(".btnSpinner").removeClass("on")
                // always executed
            });

        },
        order: (data) => {
          console.log("data: ", data)
          // const eventList = $(".dayHolder[data-value='yesterday'] .eventList ul")
          // var content = eventList.children()
          // var drop = []

          // console.log("content: ", content)

          // for(var i = 0; i < content.length; i++) {
            
          // }
        },
        showButton: (visible) => {
          if(visible) blustock.template.sidebar.button.show("right")
          else blustock.template.sidebar.button.hide("right")
        },
        change: {
          toggle: (id) => {
            const self = events.dashboard.list.change

            const eventFilterList = $(".eventFilterList")
            const item = eventFilterList.find("li[data-id='" + id + "']")
            const checkbox = item.find(".iCheck")

            const isChecked = checkbox.hasClass("checked")

            if(!isChecked) self.show(id)
            else self.hide(id)

            events.dashboard.list.showButton(true)

            const list = $(".eventFilterList").children()
            events.dashboard.list.record(list)
          },
          show: (id) => {
            const eventListController = $(".eventList ul")
            const eventFilterList = $(".eventFilterList")
            const eventItem = eventListController.find("li[data-id='" + id + "']")
            const item = eventFilterList.find("li[data-id='" + id + "']")
            const checkbox = item.find(".iCheck")
                  checkbox.addClass("checked")
                          .prop("checked",true)

            eventItem.removeClass("off")
            // eventFilterList.find("li[data-id='" + id + "']")
          },
          hide: (id) => {
            const eventListController = $(".eventList ul")
            const eventFilterList = $(".eventFilterList")
            const eventItem = eventListController.find("li[data-id='" + id + "']")
            const item = eventFilterList.find("li[data-id='" + id + "']")
            const checkbox = item.find(".iCheck")
                  checkbox.removeClass("checked")
                          .prop("checked",false)

            eventItem.addClass("off")
            
          },
          selectAll: {
            toggle: (target) => {
              const checkbox = target.find("input[type='checkbox']")
              const isChecked =  checkbox.prop("checked")
              const self = events.dashboard.list.change.selectAll

              if(isChecked) self.unselect()
              else self.select()

              events.dashboard.list.showButton(true)

              const list = $(".eventFilterList").children()
              events.dashboard.list.record(list)
            },
            select: () => {
              $(".eventFilterList").find("li").click()

              $(".eventAllToggle").find("input[type='checkbox']").prop("checked",true)
              $(".eventAllToggle").find(".iCheck").addClass("checked")
            },
            unselect: () => {
              $(".eventFilterList").find("li .iCheck.checked").closest("li").click()
              
              $(".eventAllToggle").find("input[type='checkbox']").prop("checked",false)
              $(".eventAllToggle").find(".iCheck").removeClass("checked")
            }
          }
        }
      },
      loadData: (data,date) => {
        console.log("data: ",data)
        console.log("date: ",date)

        const self = events.dashboard
        const target = data.selecteds[0]
        const unitID = target.id_unit
        const description = target.description

        console.log("target: ",target)
        console.log("unitID: ",unitID)
        console.log("description: ",description)

        $(".timelineVehicle").text(" - " + description)

        events.options.data = data
        
        const today = moment()

        var dropDate
        const dateSelected = $(".timelineDateSet").val().length > 0
        
        if(dateSelected) {
          dropDate = events.options.currentDate
        }else{
          $(".timelineDateSet").val(today.format("DD/MM/YYYY"))
          dropDate = (typeof date === "undefined" ? today.format("YYYY-MM-DD") : date)
        }
        
        // console.log("dropDate: ",dropDate)
        if(events.options.cache !== null){
          events.dashboard.gantt.timeline.trips(events.options.cache.objFinal.trips,dropDate)
          blustock.template.spinner.off()
        }else{
          const route = '/gettripbyasset'
          
          axios({
            method: 'post',
            url: route,
            timeout: 300000,
            data: {
              "unit_id": unitID,
              "date": dropDate
            }
          })
          .then(function (response) {

            //console.log(response)
            // handle success
            events.options.cache = response.data
            events.options.currentDate = dropDate 

            events.dashboard.setGoToMap(unitID,dropDate)
            // $(".timelineDateSet").val("")
            const ganttEmpty = $(".ganttEmpty")

            if(response.data.objFinal.trips.length === 0){
              if(!ganttEmpty.hasClass("on")) ganttEmpty.addClass("on")
            }else{
              ganttEmpty.removeClass("on")
              events.dashboard.gantt.timeline.trips(response.data.objFinal.trips,dropDate)
            }
          })
          .catch(function (error) {
            // handle error
            console.log("error: ", error);
          })
          .then(function () {
            blustock.template.spinner.off()
            blustock.filter.close()
            // always executed
          });
        }
        
        // mixlabs.global.ajax.get(tacodataUrl, '',dataType,contentType, tacodata.dashboard.gantt.run);
      },
      dateBar: {
        set: (date) => {
          const today = (typeof date === "undefined" || date === "" ? moment().format("DD/MM/YYYY") : moment(date,"YYYY-MM-DD").format("DD/MM/YYYY"))
          const yesterday = (typeof date === "undefined" || date === "" ? moment().subtract(1,'days').format("DD/MM/YYYY") : moment(date,"YYYY-MM-DD").subtract(1,'days').format("DD/MM/YYYY"))
          const tomorrow = (typeof date === "undefined" || date === "" ? moment().add(1,'days').format("DD/MM/YYYY") : moment(date,"YYYY-MM-DD").add(1,'days').format("DD/MM/YYYY"))

          $('.startDate').text(yesterday)
          $('.endDate').text(tomorrow)

          $(".dayHolder[data-value='yesterday']").attr("data-set",yesterday)
          $(".dayHolder[data-value='today']").attr("data-set",today)
          $(".dayHolder[data-value='tomorrow']").attr("data-set",tomorrow)
        },
        scroll: () => {

        }
      },
      resize: () => {
        const self = events.dashboard
        if(events.options.data !== null){
          blustock.template.spinner.on()
        }else{
          blustock.template.spinner.off()
        }
        
        self.gantt.ruler(events.options.range)
        setTimeout(() => {
          if(events.options.data !== null){
            self.loadData(events.options.data,events.options.currentDate)
          }
        },250)
      },
      setGoToMap: (unitID,date) => {
        const unitId = unitID
        const end = date
        
        var start = moment(end,"YYYY-MM-DD")
            start = start.subtract(1, "days")
            start = start.format("YYYY-MM-DD")

        const url = "/detalhes_viagem?unitid=" + unitID + "&start=" + start + "&end=" + end

        $(".goToMap").attr("href",url).removeClass("nolink")
      },
      gantt:{
        ruler: (range) => {
          // console.log('range',range)
          const rulerController = $('.timelineRuler ul')
                rulerController.empty()

          const panelWidth = $('.scrollPanel').width()
          const width = panelWidth/range + 'px'
          const totalTime = 24

          for(var i = 0; i < totalTime; i++){
            const rulerItem = "<li class='ruler_hour' style='width: " + width + "'><div>"+
                              "<label>" + moment(i,'h').format('HH:mm') + "</label>"+
                              "<span></span>"+
                              "<span></span>"+
                              "<span></span>"+
                              "<span></span>"+
                              "<span></span>"+
                              "</div>"+
                              "</li>"

            rulerController.append(rulerItem)
          }

          $('.scrollPanel').scrollLeft(rulerController.width())
        },
        setRange: (range) => {
          events.options.range = range
          $(".rangeSelect").removeClass("selected")
          $(".rangeSelect[data-val='" + range + "']").addClass("selected")

          events.dashboard.resize()
        },
        timeline: {
          trips: (data,date) => {
            console.log("data:",data)
            console.log("date:",date)
            
            $('.timelineGantt>div').empty()
            $('.eventList ul li > div').empty()
            $('.timelineGantt>div.evl').empty()

            events.dashboard.dateBar.set(date)

            for(var i = 0; i < data.length; i++){
              var TripStart = data[i].TripStart
                  TripStart = TripStart.split(" ")[0] + " " + TripStart.split(" ")[1]

              var TripEnd = data[i].TripEnd
                  TripEnd = TripEnd.split(" ")[0] + " " + TripEnd.split(" ")[1]

              const sw = $('.ruler_hour').width()/3600
              
              var ms = moment(TripEnd,"YYYY-MM-DD HH:mm:ss.SSS").diff(moment(TripStart,"YYYY-MM-DD HH:mm:ss.SSS"))
                  ms = ms/1000

              const TripStartTime = moment(TripStart,"YYYY-MM-DD HH:mm:ss.SSS")
              const TripEndTime = moment(TripEnd,"YYYY-MM-DD HH:mm:ss.SSS")
              const tripStartDate = TripStartTime.format("DD/MM/YYYY")

              var same = TripStartTime.isSame(TripEndTime,"day")
                  same = (same ? TripStartTime.isSame(date,"day") : same)

              const dateDayStart =  moment(TripStartTime).startOf('day')
              const st = TripStartTime.diff(dateDayStart, 'seconds')
              const totalW = $(".dayHolder").width()
              
              var l = st*sw
                  l = (same ? l + totalW : l)

              const distanceSuffix = (Number(data[i].Distance) > 1 ? " mts" : " m")
              
              var w = ms*sw
                  w = (w < 1 ? 1 : w)

              const name = (data[i].driver === null ? data[i].DriverId : data[i].driver.name)

              const style = "width: " + w + "px; left: " + l + "px"
              const contentBubble = moment(data[i].TripStart,"YYYY-MM-DD HH:mm:ss.SSS").format("DD/MM/YYYY - HH:mm:ss") + "*" +
                                    moment(data[i].TripEnd,"YYYY-MM-DD HH:mm:ss.SSS").format("DD/MM/YYYY - HH:mm:ss") + "*" +
                                    moment().startOf('day').seconds(data[i].Duration).format('HH:mm:ss') + "*" + 
                                    data[i].Distance + distanceSuffix + "*" + name

              const item = "<span class='tripLine' style='" + style + "' bubble-type='trip' content-bubble='" + contentBubble + "'></span>"
              
              $(".dayHolder[data-value='yesterday'] .timelineGantt>div.tml").append(item)
              events.dashboard.gantt.timeline.events.list(data[i].events)
            }
          },
          events: {
            list: (data) => {
              // console.log('data: ',data)
              const content = data[0]
              const eventList = events.options.eventList
              const eventListController = $(".eventList ul")

              // for(var i = 0; i < content.length; i++){
              //   const exists = eventList.indexOf(content[i].EventId) !== -1
              //   if(!exists) {
              //     eventList.push(content[i].EventId)
                  
              //     const evenName = events.options.eventNames[content[i].EventId]

              //     const item = "<li data-id='" + content[i].EventId + "'>"+
              //                  "<p>" + evenName + "</p>"+
              //                  "<div></div></li>"

              //     eventListController.append(item)
              //   }
              // }
              events.dashboard.gantt.timeline.events.add(content)
              $(".scrollPanel").scroll()
              // console.log('eventList: ',eventList)
            },
            add: (data) => {
              // console.log('event data: ',data)
              const sw = $('.ruler_hour').width()/3600
              
              for(var i = 0; i < data.length; i++){

                var startTime = data[i].StartDateTime.split(" ")
                    startTime = startTime[0] + " " + startTime[1]
                
                var endTime = data[i].EndDateTime.split(" ")
                    endTime = endTime[0] + " " + endTime[1]

                const eventStartTime = moment(startTime,"YYYY-MM-DD HH:mm:ss.SSS")
                const eventEndTime = moment(endTime,"YYYY-MM-DD HH:mm:ss.SSS")
                const eventStartDate = eventStartTime.format("DD/MM/YYYY")
                const dateDayStart =  moment(eventStartTime).startOf('day')
                const st = eventStartTime.diff(dateDayStart, 'seconds')

                const left = st*sw
                // console.log('eventStartDate: ',eventStartDate)

                const title = "Data: " + eventStartTime.format("DD/MM/YYYY - HH:mm:ss") +
                              // "&#013;Duração: " + moment().startOf('day').seconds(data[i].Duration).format('HH:mm:ss') +
                              "&#013;Valor: " + data[i].Value
                // title = ""
                const contentBubble = eventStartTime.format("DD/MM/YYYY - HH:mm:ss") + "*" +
                                      eventEndTime.format("DD/MM/YYYY - HH:mm:ss") + "*" + 
                                      data[i].TotalTimeSeconds + "s*" + data[i].Value 


                // console.log("Content Bubble => " + contentBubble)
                const item = "<span class='eventLine' style='left: " + left + "px' bubble-type='event' content-bubble='" + contentBubble + "'></span>"

                $(".dayHolder[data-set='" + eventStartDate + "'] .eventList ul").find("li[data-id='" + data[i].EventId + "'] div").append(item)
                $(".dayHolder[data-set='" + eventStartDate + "'] .timelineGantt>div.evl").append(item)
              }
            }
          }
        },
        lockLabel: (offset) => {
          const target = $(".dayHolder[data-value='yesterday'] .eventList li>p")
          const pos = offset + 15

          target.css('left',pos)
        },
        bubble: {
          open: (e,type,data) => {
            // console.log("open bubble data: ", data)

            const content = data.split("*")
            const x = e.pageX
            const y = e.pageY
            const xMax = window.innerWidth - 300
            const yMax = window.innerHeight - 200

            // console.log("x: ",x)
            // console.log("y: ",y)
            // console.log("xMax: ",xMax)
            // console.log("yMax: ",yMax)

            const xDrop = (x > xMax)
            const yDrop = (y > yMax)

            // console.log("xDrop: ",xDrop)
            // console.log("yDrop: ",yDrop)

            var style = "left: " + x + "px; top: " + y + "px;"

            if(xDrop && yDrop){
              style += "transform: translateX(-100%) translateY(-100%); "
            }else{
              if(xDrop) style += "transform: translateX(-100%); "
              if(yDrop) style += "transform: translateY(-100%); "
            }
            
            
            var item = "<div class='eventDashBubble' style='" + style + "'><ul>"
                if(type === "trip"){
                  
                  const inicio = content[0]
                  const fim = content[1]
                  const duracao = content[2]
                  const distancia = content[3]
                  const motorista = content[4]

                  item +="<li><h4>VIAGEM</h4></li>" +
                         "<li>Início: <b>" + inicio + "</b></li>" + 
                         "<li>Fim: <b>" + fim + "</b></li>" + 
                         "<li>Duração: <b>" + duracao + "</b></li>" + 
                         "<li>Distância: <b>" + distancia + "</b></li>" +
                         "<li>Motorista: <b>" + motorista + "</b></li>"
                }
                if(type === "event"){
                  const inicio = content[0]
                  const fim = content[1]
                  const duracao = content[2]
                  const valor = Number(content[3])

                  item +="<li><h4>EVENTO</h4></li>" +
                         "<li>Inicio: <b>" + inicio + "</b></li>" +
                         "<li>Fim: <b>" + fim + "</b></li>" +
                         "<li>Duração: <b>" + duracao + "</b></li>"


                  //REMOVER ESTE IF QUANDO LEANDRO RESOLVER VALORES VINDO IGUAIS A 0
                  if(valor !== 0) {
                    item += "<li>Valor: <b>" + valor + "</b></li>"
                  }
                }
                item +="</ul></div>"

            if($('.eventDashBubble').length === 0) $('body').prepend(item)
          },
          close: () => {
            $(".eventDashBubble").remove()
          }
        },
        line: {
          show: (x,y) => {
            const line = "<span class='cursorY'></span>"
            const cursorY = $(".cursorY")
            const elementTop = $(".timelineActions").outerHeight() + $(".timelineHeader").outerHeight()
            
            if(cursorY.length === 0) {
              $(".ganttMaster").prepend(line)
            }else{
              cursorY.css({
                "left": x + "px",
                //"height": $(".scrollPanel").height() - elementTop + "px",
                  "top": elementTop + "px"
              })
            }


          },
          hide: () => {
            
          }
        },
        run: function (data){
            const self = events.dashboard.gantt
            self.ruler(events.options.range)
        }
      }
    },
    events: () => {
      if(!events.options.eventsLoaded){
        $(".scrollPanel").on('scroll',function(e){
          const scrollPos = $(this).scrollLeft()
          events.dashboard.gantt.lockLabel(scrollPos)
          // console.log('scrollPos',scrollPos)
        })

        $(document).on('click','.rangeSelect',function(e){
          const newRange = Number($(this).attr('data-val'))
          events.dashboard.gantt.setRange(newRange)
        })
        
        $(window).on('resize',function(e){
          clearTimeout(events.options.resizeTimeout)
          events.options.resizeTimeout = setTimeout(() => {
            events.dashboard.resize()
          },250)
        })

        $(document).on('change','.dateSelect input',function(e){
          const newDate = moment($(this).val(), "DD/MM/YYYY").format("YYYY-MM-DD")
          events.options.cache = null
          events.options.currentDate = newDate

          if(events.options.data !== null){
            blustock.template.spinner.on()
            events.dashboard.loadData(events.options.data,newDate)
          }else{

            blustock.filter.open(true)
            // alert("Selecione um veículo para continuar")
          }
          
        })

        $(document).on('mouseenter','.tripLine',function(e){
          const data = $(this).attr("content-bubble")
          const type = $(this).attr("bubble-type")

          events.dashboard.gantt.bubble.open(e,type,data)
        })
        $(document).on('mouseleave','.tripLine',function(e){
          events.dashboard.gantt.bubble.close()
        })

        $(document).on('mouseenter','.eventLine',function(e){
          const data = $(this).attr("content-bubble")
          const type = $(this).attr("bubble-type")

          events.dashboard.gantt.bubble.open(e,type,data)
        })

        $(document).on('mouseleave','.eventLine',function(e){
          events.dashboard.gantt.bubble.close()
        })

        $(document).on('click','.eventFilterList li',function(e){
          const id = $(this).attr("data-id")
          events.dashboard.list.change.toggle(id)
        })
        
        $(document).on('mousemove','.ganttMaster .scrollPanel',function(e){
          const offset = $(this).offset()

          const x = Math.floor(e.pageX - offset.left)
          const y = Math.floor(e.pageY - offset.top)

          events.dashboard.gantt.line.show(x,y)
        })

        $(document).on('mousedown mouseup','.ganttMaster .scrollPanel',function(e){
          const onClickPos = e.pageX
          const currentPos = $(this).scrollLeft()

          if(e.type === "mousedown"){
            $(this).on("mousemove",function(e){
              const onMovePos = e.pageX
              const dif = onClickPos - onMovePos
  
              $(this).scrollLeft(currentPos + dif)
            })
          }
          if(e.type === "mouseup"){
            $(this).unbind('mousemove')
            $(this).scrollLeft($(this).scrollLeft())
          }
        })

        $(".eventFilterList").on("sortbeforestop", function( e, ui ) {
            const list = $(".eventFilterList").children()
            events.dashboard.list.record(list)
        })

        $(document).on('click','.eventAllToggle',function(e){
          events.dashboard.list.change.selectAll.toggle($(this))
        })

        $(document).on("click", '.saveEventListConfig',function(e){
          events.dashboard.list.save()
        })

        events.options.eventsLoaded = true
      }

      
    },
    run: () => {
      events.dashboard.start()
      events.events()
    }
  }

  events.run()
})