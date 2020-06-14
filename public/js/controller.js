$(document).ready(function(){

  window.blustock = {
    options: {
      eventsLoaded: false,
      isMobile: false
    },
    tools: {
      dataTables:{
        options: {
          timeout: null,
        },
        colReorder:{
          set: (uri, columns) => {
            blustock.tools.dataTables.options.timeout = setTimeout(() => {
              var titles = []
              var names = []
  
              for (var i in columns) {
                if(columns[i].sTitle !== "action" && columns[i].data !== "action"){
                  titles.push(columns[i].sTitle)
                  names.push(columns[i].data)
                }
              }
  
              var drop = {
                titles: titles.toString(),
                names: names.toString(),
                model: uri
              }
  
              blustock.tools.dataTables.colReorder.record(drop)
            },300)
          },
          record: (data) => {
            blustock.tools.dataTables.colReorder.spinner.add()
  
            axios.post('/datatables_order',data)
            .then(function (response) {
              blustock.tools.dataTables.colReorder.statusBar.show()
              console.log("response: ", response);
            })
            .catch(function (error) {
              // handle error
              console.log("error: ", error);
            })
            .then(function () {
              blustock.tools.dataTables.colReorder.statusBar.hide()
              blustock.tools.dataTables.colReorder.spinner.remove()
              // always executed
              // self.progress.remove()
            });
          },
          spinner: {
            add: () => {
              if($(".colReorderSpinner").length > 0) $(".colReorderSpinner").remove()
              const spinner = "<div class='colReorderSpinner'><i class='fas fa-spinner fa-pulse'></i></div>"
              $(".dt-buttons").append(spinner)
            },
            remove: () => {
              setTimeout(() => {
                $(".colReorderSpinner").remove()
              },500)
            }
          },
          statusBar: {
            show: () => {
              $(".statusbar").remove()
  
              const statusbar = "<div class='statusbar success off'>Ordenação de colunas salva com sucesso</div>"
              // console.log("statusbar:",statusbar)
              $(statusbar).insertBefore("#dataTableBuilder").removeClass("off")
            },
            hide: () => {
              // console.log("close")
              setTimeout(() => {
                $(".statusbar").addClass("off")
                setTimeout(() => {
                  $(".statusbar").remove()
                },500)
              },5000)
            }
          }
        }
      }
    },
    form: {
      checkbox: {
        run: () => {
          $(".iCheck").iCheck({
            checkboxClass: 'icheckbox_flat-green iCheck',
            // radioClass   : 'iCheck'
          })
  
          $(".iRadio").iCheck({
            // checkboxClass: 'icheckbox_flat-green iRadio',
            radioClass   : 'iradio_flat-green iRadio'
          })
        }
      },
      keyControl: (key) => {
        switch(key){
          case 27: //esc
            // blustock.template.sidemenu.close()
          break;
        }
      },
      labelControl: {
        add: (el) => {
          if(el.val().length === 0){
            var placeholder = el.attr("placeholder")
  
            el.attr("placeholder-cache",placeholder)
            el.attr("placeholder","")
      
            const feedback = el.parent().find(".formControlFeedback")
                  feedback.addClass("on")
          }
        },
        remove: (el) => {
          if(el.val().length === 0){
            const feedback = el.parent().find(".formControlFeedback")
                  feedback.removeClass("on")
  
            var placeholder = el.attr("placeholder-cache")
                el.attr("placeholder",placeholder)
          }
        }
      },
    },
    template: {
      // topmenu: {
      //   hover: (id,double) => {

      //     const self = blustock.template.topmenu
      //     clearTimeout(blustock.template.topmenu.balloon.timeout)

      //     const isOn = $(".topMenuBalloon").hasClass("on")

      //     let callback = (isOn ? 'self.balloon.change(id,true)' : 'self.balloon.open(id)')
      //     self.balloon.setSize(double,'self.balloon.open(id)',id)

      //   },
      //   leave: () => {
      //     blustock.template.topmenu.balloon.timeout = setTimeout(() => {
      //       blustock.template.topmenu.balloon.close()
      //     },600)
      //   },
      //   balloon: {
      //     timeout: null,
      //     setSize: (double, callback, id) => {
      //       const self = blustock.template.topmenu

      //       $(".topMenuBalloon").addClass("noTransition")
            
      //       setTimeout(() => {
      //         eval(callback)
      //       },200)
            
      //     },
      //     open: (id) => {
      //       $(".topMenuBalloon").addClass("on")
      //       // $(".topMenuBalloon").addClass('noTransition')
      //       blustock.template.topmenu.balloon.change(id,false)
      //       blustock.template.topmenu.balloon.move(id,true)
      //     },
      //     close: (id) => {
      //         $(".topMenuBalloon").removeClass("on")
      //         $(".submenuController").removeClass("on")
              
      //         $(".topMenuBalloon").addClass('noTransition')
      //                             .removeClass("double")
      //                             .removeClass('noTransition')
      //     },
      //     move: (id,open) => {
      //       const target = $(".topBalloonControl[menu-id='" + id + "']")
      //       const balloon = $(".topMenuBalloon")
      //       const offset = target.offset().left
      //       const desloc = target.innerWidth()/2
      //       const balloonW = balloon.width()
      //       const pos = offset + desloc - (balloonW / 2)

      //       if(open){
      //         balloon.removeClass("noTransition").css({
      //           left: pos
      //         })
      //       }else{
      //         balloon.removeClass("noTransition").css({
      //           left: pos
      //         })
      //       }
      //     },
      //     change: (id,move) => {
      //       if(move){
      //         blustock.template.topmenu.balloon.move(id)
      //       }
            
      //       $(".submenuController").addClass('noTransition')
      //                              .removeClass("on")

      //       $(".submenuController[sub-id='" + id + "']").addClass('noTransition')
      //                                                   .addClass("on")
      //                                                   .removeClass("noTransition")

      //       $(".submenuController").removeClass('noTransition')
      //     }
      //   },
      // },
      spinner: {
        on: () => {
          if(!$('.appLoader').hasClass('on')){
            $('.appLoader').addClass('on')
          }
        },
        off: () => {
          $('.appLoader').removeClass('on')
        }
      },
      login: {
        start: () => {
          $('.loginBox').removeClass("closed")
        },
        sidebar: {
          track: (e) => {
            const x = e.pageX
            const offset = window.innerWidth - (window.innerWidth*.3)
            const login = $('.loginBox')
  
            if(x > offset){
              if(!login.hasClass("out")) blustock.template.login.sidebar.move("on")
            }else{
              blustock.template.login.sidebar.move("out")
            }
          },
          move: (state) => {
            switch(state){
              case "on":
                $('.loginBox').addClass("out")
              break;
              case "out":
                $('.loginBox').removeClass("out")
              break;
  
            }
          }
        }
      },
      logout:{
        show: () => {
          $(".btSair").addClass("on")
        },
        hide: () => {
          $(".btSair").removeClass("on")
        }
      },
      run: () => {
        
      },
      modal: {
        toggle: (controller) => {
          const self = blustock.template.modal
  
          const modal = $(".appModal[modal-control='" + controller + "']")
          const isOpen = modal.hasClass("on")
  
          if(isOpen) self.close(controller)
          else self.open(controller)
  
        },
        open: (controller) => {
          const modal = $(".appModal[modal-control='" + controller + "']")
                modal.addClass("on")
        },
        close: (controller) => {
          const modal = $(".appModal[modal-control='" + controller + "']")
                modal.removeClass("on")
        },
        populate: () => {
          
        }
      },
      sidebar: {
        options: {
          eventsLoaded: false
        },
        toggle: (side) => {
          console.log("side: ",side)

          const bar = $("." + side + "bar")
          const isOn = bar.hasClass("on")
          const self = blustock.template.sidebar
          console.log("isOn: ",isOn)

          if(isOn) self.close(side)
          else self.open(side)
        },
        open: (side) => {
          $("." + side + "bar").addClass("on")
        },
        close: (side) => {
          if(typeof side === "undefined"){
            $("rightbar").removeClass("on")
            $("leftbar").removeClass("on")
          }else{
            $("." + side + "bar").removeClass("on")
          }
        },
        events: () => {
          const self = blustock.template.sidebar
          if(!self.options.eventsLoaded){
            $(document).on("click",".sidebarToggle",function(e){
              const controller = $(this).attr("sidebar-control")
              self.toggle(controller)
            })

            self.options.eventsLoaded = true
          }
        },
        run: () => {
          const self = blustock.template.sidebar
          self.events()
        },
        button:{
          show: (side) => {
            const bar = $("." + side + "bar")

            bar.find(".sidebarAction").addClass("on")
            bar.find(".sidebarContent").addClass("actionOn")
          },
          hide: (side) => {
            const bar = $("." + side + "bar")

            bar.find(".sidebarAction").removeClass("on")
            bar.find(".sidebarContent").removeClass("actionOn")
          }
        }
      },
      collapse: {
        options: {
          eventsLoaded: false
        },
        run: () => {
          const self = blustock.template.collapse
  
          self.events()
        },
        toggle: (id) => {
          const self = blustock.template.collapse
          const target = $(".collapse-body[item-id='" + id +"']")
          const isOn = target.closest(".collapse-item").hasClass("on")
  
          if(isOn) self.close(id)
          else self.open(id)
        },
        open: (id) => {
          const target = $(".collapse-body[item-id='" + id +"']")
          const parent = target.closest(".collapse-item")
          $(".collapse-item").removeClass("on")
          $(".collapse-content").removeClass("on")
  
          parent.addClass("on")
          setTimeout(() => {
            parent.find(".collapse-content").addClass("on")
          },300)
        },
        close: (target) => {
          $(".collapse-content").removeClass("on")
          setTimeout(() => {
            $(".collapse-item").removeClass("on")
          },300)
        },
        events: () => {
          const self = blustock.template.collapse
          
          if(!blustock.template.collapse.options.eventsLoaded){
            $(document).on("click",".collapse-toggle",function(e){
              const id = $(this).attr("toggle-control")
  
              self.toggle(id)
            })
  
            blustock.template.collapse.options.eventsLoaded = true
          }
        }
      },
      header: {
        control: () => {
          const self = blustock.template.header
          var mousemoveTimeout
          // console.log("header control start")

          mousemoveTimeout = setTimeout (() => {
            self.close()
            // window.maps.resize()
          }, 5000)

          $(document).on("mousemove", function (e) {
            var ypos = e.clientY
            // console.log("ypos: ", ypos)

            if (ypos < 120) {
              self.open()              
            }else{
              clearTimeout(mousemoveTimeout)
              mousemoveTimeout = setTimeout (() => {
                console.log("header control restart")
                self.close()
                // window.maps.resize()
              }, 5000)
            }
          })
        },
        open: () => {
          $("#mainHeader").removeClass("closed")
          $(".main-tool").addClass("headerOpened")
        },
        close: () => {
          $("#mainHeader").addClass("closed")
          $(".main-tool").removeClass("headerOpened")
        }
      }
    },
    filter: {
      options: {
        eventsLoaded: false,
        selected: {
          subgroups: [],
          sites: [],
          assets: []
        }
      },
      open: (single) => {
        const self = blustock.filter
        const singleClass = (single ? "single" : "")

        // console.log('open!!!!')
        $(".modalFilter .treeView .scrollView").each(function(i){
          $(this).empty()
        })
        
        self.options.selected.subgroups = []
        self.options.selected.sites = []
        self.options.selected.assets = []

        self.allowSubmit()
        
        $(".modalFilter").addClass("open").addClass(singleClass)
        self.getData(true)
      },
      close: () => {
        $(".modalFilter").removeClass("open")
      },
      clean: (id,parent) => {  
        parent = (parent === 'sites' ? 'assets' : parent)
        parent = (parent === 'subgroups' ? 'sites' : parent)

        if(id === "all"){
          $(".scrollView ul[item-id='" + parent + "']").remove()
        }else{
          $(".scrollView ul[item-id='" + parent + "'][data-id='" + id + "']").remove()
        }
        // console.log('clean parent: ', parent)
      },
      fill: (data,parent) => {
        const self = blustock.filter
        
        const all_subgroups = eval(data.all_subgroups)
        const type = (all_subgroups ? 'subgroups' : data.type)

        var drop = []
        const itemPrefix = "<ul data-id='" + parent + "' item-id='"+ type.toLowerCase() +"'>"
        const itemSuffix = "</ul>"
        var item

        // console.log("type: ", type)
        var isSingle = $(".treeView").hasClass("single")
        var isChild = (type === "Assets" || type === "Drivers" ? "child" : "")
        // console.log('selecteds: ',data.selecteds)

        $.each(data.selecteds,function(k,v){
          const dropID = (type.toLowerCase() === 'assets' ? v.id_unit : v.id)
          const name = (typeof v.name !== "undefined" ? v.name : v.description)

          item = "<li class='itemSelect'><label class='" + isChild + "' onclick=blustock.filter.select($(this)," + dropID + ")>"
          if(isSingle){
            item += "<input type='radio' class='iRadio' id='" + dropID + "' name='" + type.toLowerCase() + "'>"
          }else{
            item += "<input type='checkbox' class='iCheck' id='" + dropID + "'>"
          }
          
          item += "<span class='text'>" + name + "</span>"+
                  "</label></li>"

          drop.push(item)
        })

        const finalDrop = itemPrefix + drop.join("") + itemSuffix

        if(all_subgroups){
          $(".treeView .scrollView[item-id='subgroups']").append(finalDrop)
        }else{
          $(".treeView .scrollView[item-id='" + data.type.toLowerCase() + "']").append(finalDrop)
        }

        // self.options.eventsLoaded = false
        // self.events()
        setTimeout(() => {
          blustock.form.checkbox.run()
        },100)
        
      },
      getData: (ini,options) => {
        const self = blustock.filter
        var params;

        if(ini){
          params = {
            "all_subgroups": true,
            "type": "",
            "selecteds": [],
            "is_child": true
          }
        }else{
          params = options
        }

        console.log('options:' , options)
        const parentID = (typeof options === "undefined" ? 0 : options.selecteds[0])
        const type = (typeof options === "undefined" ? 'subgroups' : options.type)

        var dropType;
        const all = params['all_subgroups']

        if (all){
          dropType = 'subgroups'
        }else{
          if(type === 'sites') dropType = 'assets'
          if(type === 'subgroups') dropType = 'sites'
        }
        
        // console.log('dropType:' , dropType)

        self.progress.add(dropType)

        axios.get('getfilter',{
          params: params
        })
        .then(function (response) {
          // handle success
          self.fill(response.data,parentID)
          // console.log("response: ", response);
        })
        .catch(function (error) {
          // handle error
          console.log("error: ", error);
        })
        .then(function () {
          // always executed
          self.progress.remove()
        });
      },
      select: (item,id) => {
        const self = blustock.filter

        setTimeout(() => {
          const parent = item.closest('ul').attr('item-id')
          const state = item.find('.icheckbox_flat-green').hasClass('checked')
          const isChild = item.hasClass("child")
          const isSingle = $(".treeView").hasClass("single")

          console.log("parent: ",parent)
          if(isSingle){
            if(isChild){
              self.options.selected.assets = []
              self.options.selected.assets.push(id)
            }else{
              const options = {
                "all_subgroups": false,
                "type": parent.toLowerCase(),
                "selecteds": [id],
                "is_child": true
              }
              console.log("options: ",options)

              self.clean('all',parent.toLowerCase())
              self.getData(false,options)
            }
          }else{
            if(state){
              if(isChild){
                self.options.selected.assets.push(id)
              }else{
                const options = {
                  "all_subgroups": false,
                  "type": parent.toLowerCase(),
                  "selecteds": [id],
                  "is_child": true
                }
                
                if(parent.toLowerCase() === 'subgroups') self.options.selected.subgroups.push(id)
                if(parent.toLowerCase() === 'sites') self.options.selected.sites.push(id)

                self.getData(false,options)
              }
            }else{
              if(isChild){
                const assetsItemIndex = self.options.selected.assets.indexOf(id)
                self.options.selected.assets.splice(assetsItemIndex,1)
              }else{
                self.clean(id,parent.toLowerCase())
                
                if(parent.toLowerCase() === 'subgroups'){
                  const subgroupsItemIndex = self.options.selected.subgroups.indexOf(id)
                  self.options.selected.subgroups.splice(subgroupsItemIndex,1)

                  const sitesItemIndex = self.options.selected.sites.indexOf(id)
                  self.options.selected.sites.splice(sitesItemIndex,1)

                  const assetsItemIndex = self.options.selected.assets.indexOf(id)
                  self.options.selected.assets.splice(assetsItemIndex,1)
                }
                if(parent.toLowerCase() === 'sites'){
                  const sitesItemIndex = self.options.selected.sites.indexOf(id)
                  self.options.selected.sites.splice(sitesItemIndex,1)
                  
                  const assetsItemIndex = self.options.selected.assets.indexOf(id)
                  self.options.selected.assets.splice(assetsItemIndex,1)
                }
              }
            }
          }          

          self.allowSubmit()
          // console.log('item: ',item)
          // console.log('id: ',id)
          // console.log('parent: ',parent)
          // console.log('state: ',state)
        },100)
      },
      clearSelection: () => {
        const self = blustock.filter

        self.options.selected.subgroups = []
        self.options.selected.sites = []
        self.options.selected.assets = []

        $('.treeView div.iCheck').removeClass('checked')

      },
      progress: {
        add: (type) => {
          // console.log('type',type)
          $(".scrollView label").addClass("disabled")

          const loader = "<div class='treeListSpinner'><i class='mi-spinner_ring'></i> Carregando...</div>"
          $(".scrollView[item-id='" + type + "'").append(loader)

        },
        remove: () => {
          $(".scrollView label").removeClass("disabled")
          $('.treeListSpinner').remove()
        }
      },
      search: {
        select: (item,id) => {
          const filter = blustock.filter
          const parent = item.closest('.scrollView').attr('item-id')
          const state = item.find('.icheckbox_flat-green').hasClass('checked')
          const isChild = item.hasClass("child")
          const isSingle = $('.treeView').hasClass("single")

          setTimeout(() => {
            if(isSingle){
              filter.options.selected[parent] = [id]
            }else{
              if(!state){
                filter.options.selected[parent].push(id)
              }else{
                const itemIndex = filter.options.selected[parent].indexOf(id)
                filter.options.selected[parent].splice(itemIndex,1)
              }
            }
            filter.allowSubmit()

            // console.log('selecteds: ', filter.options.selected)
          },300)
          
        },
        populate: (data) => {
          const self = blustock.filter.search
          console.log('populate data:', data)
          self.clear()

          const resultController = $('.modalFilter .searchResult')
                resultController.addClass("on")
                
          var isSingle = $(".treeView").hasClass("single")

          var subgroups = []
          var sites = []
          var assets = []

          if(data.subgroups.length > 0){
            for(var i = 0; i < data.subgroups.length; i++){
              var item = "<li item-id='" + data.subgroups[i].id + "'>"
                  item+= "<label onclick=blustock.filter.search.select($(this),'" + data.subgroups[i].id + "')>"
                  if(isSingle){
                    item+= "<input type='radio' class='iRadio' id='" + data.subgroups[i].id + "' name='subgroups'>"
                  }else{
                    item+= "<input type='checkbox' class='iCheck' id='" + data.subgroups[i].id + "'>"
                  }
                  
                  item+= "<span class='text'>" + data.subgroups[i].name + "</span>"
                  item+= "</label>"
                  item+= "</li>"
  
              subgroups.push(item)
            }

            $(".searchResult .scrollView[item-id='subgroups'] ul").append(subgroups.join(""))
          }
          if(data.Sites.length > 0){
            for(var i = 0; i < data.Sites.length; i++){
              var item = "<li item-id='" + data.Sites[i].id + "'>"
                  item+= "<label onclick=blustock.filter.search.select($(this),'" + data.Sites[i].id + "')>"
                  if(isSingle){
                    item+= "<input type='radio' class='iRadio' id='" + data.Sites[i].id + "' name='sites'>"
                  }else{
                    item+= "<input type='checkbox' class='iCheck' id='" + data.Sites[i].id + "'>"
                  }
                  item+= "<span class='text'>" + data.Sites[i].name + "</span>"
                  item+= "</label>"
                  item+= "</li>"
              
              sites.push(item)
            }

            $(".searchResult .scrollView[item-id='sites'] ul").append(sites.join(""))
          }
          if(data.Assets.length > 0){
            for(var i = 0; i < data.Assets.length; i++){
              var item = "<li item-id='" + data.Assets[i].id_unit + "'>"
                  item+= "<label onclick=blustock.filter.search.select($(this),'" + data.Assets[i].id_unit + "')>"
                  if(isSingle){
                    item+= "<input type='radio' class='iRadio' id='" + data.Assets[i].id + "' name='assets'>"
                  }else{
                    item+= "<input type='checkbox' class='iCheck' id='" + data.Assets[i].id + "'>"
                  }
                  item+= "<span class='text'>" + data.Assets[i].description + "</span>"
                  item+= "</label>"
                  item+= "</li>"
  
              assets.push(item)
            }

            $(".searchResult .scrollView[item-id='assets'] ul").append(assets.join(""))
          }

          blustock.form.checkbox.run()
          $('.treeView').removeClass('on')
        },
        clear: () => {
          blustock.filter.clearSelection()

          $(".searchResult .scrollView li").remove()
          $('.modalFilter .searchResult').removeClass('on')
          $('.treeView').addClass('on')
        },
        query: (q) => {
          console.log('search: ', q)
          const self = blustock.filter.search

          self.progress.add()

          const searchParams = {
            string: q,
            type: 'assets'
          }
          axios.get('search',{
            params: searchParams
          })
          .then(function (response) {
            // handle success
            console.log("response: ", response)
            self.populate(response.data)
          })
          .catch(function (error) {
            // handle error
            console.log("error: ", error);
          })
          .then(function () {
            // always executed
            self.progress.remove()
          });
        },
        progress: {
          add: () => {
            $(".filterSearchSpinner").addClass("on")
            // $(".filterSearchClear").removeClass("on")
            $(".filterSearch").addClass("disabled")

            blustock.filter.search.allowClear.disable()
          },
          remove: () => {
            $(".filterSearchSpinner").removeClass("on")
            // $(".filterSearchClear").addClass("on")
            $(".filterSearch").removeClass("disabled")

            blustock.filter.search.allowClear.enable()
          }
        },
        allowClear: {
          enable: () => {
            if(!$(".filterSearchClear").hasClass("on")){
              $(".filterSearchClear").addClass("on")
            }
          },
          disable: () => {
            $(".filterSearchClear").removeClass("on")
          }
        }
      },
      allowSubmit: () => {
        const self = blustock.filter

        const allowAssets = (self.options.selected.assets.length > 0)
        const allowSites = (self.options.selected.sites.length > 0)
        const allowsubgroups = (self.options.selected.subgroups.length > 0)

        const allowSubmit = (allowAssets || allowSites || allowsubgroups)

        // console.log('allowSubmit: ',allowSubmit)
        if(allowSubmit) $('.filterSubmit').removeClass("disabled")
        else $('.filterSubmit').addClass("disabled")
      },
      submit: () => {
        const self = blustock.filter
        
        const selectedsubgroups = self.options.selected.subgroups
        const selectedSites = self.options.selected.sites
        const selectedAssets = self.options.selected.assets
        const callback = $('.treeView').attr('callback')

        var active;
        var selected;

        if(selectedsubgroups.length > 0) {
          active = 'subgroups'
          selected = selectedsubgroups
        }
        if(selectedSites.length > 0) {
          active = 'sites'
          selected = selectedSites
        }
        if(selectedAssets.length > 0) {
          active = 'assets'
          selected = selectedAssets
        }

        const params = {
          "all_subgroups": false,
          "type": active,
          "selecteds": selected,
          "is_child": false
        }

        // console.log("filter params:", params)
        blustock.template.spinner.on()

        axios.get('getfilter',{
          params: params
        })
        .then(function (response) {
          // handle success
          if(callback === 'getPositions') {
            maps.options.cache = null
            setTimeout(() => {
              self.getPositions(response.data)
            },100)
          }
          if(callback === 'getEvents') {
            events.options.cache = null
            events.dashboard.loadData(response.data)
          }
          // console.log('sucesso--------------------------')
          // eval(callback + "(" + response.data + ")")
        })
        .catch(function (error) {
          // handle error
          console.log("error: ", error);
        })
        .then(function () {
          // always executed
          // console.log('fim--------------------------')
          // self.progress.remove()
        });
      },
      getPositions: (data,setViewBounds) => {
        const self = blustock.filter
        if(maps.options.cache === null) maps.options.cache = data
        
        data = (typeof data === "undefined" || data === "" ? maps.options.cache : data)
        maps.options.setViewBounds = (typeof setViewBounds === "undefined" ? true : setViewBounds)

        console.log('data:',data)
        const content = data.selecteds
        var unitsID = []

        if(typeof content !== "undefined"){
          for(var i = 0; i < content.length; i++){
            unitsID.push(content[i].id_unit)
          }
          
          console.log("unitsID:",unitsID)

          axios.post('getlastpositionsbyassets', {
            'units_id': unitsID
          })
          .then(function (response) {
            // handle success
            // console.log("getlastpositionsbyassets - response: ", response)
            maps.positions.addAssets(response.data.lastPositionsByAssets)
          })
          .catch(function (error) {
            // handle error
            console.log("error: ", error);
          })
          .then(function () {
            // always executed
            // alert('terminou')
            blustock.template.spinner.off()
            self.close()
          })
        }else{
          // window.alert("ops! Algo aconteceu...")
          blustock.template.spinner.off()
          console.warn("Não existem dados para atualizar.")
        }
      },
      events: () => {
        const self = blustock.filter

        if(!self.options.eventsLoaded){

          $(document).on('click','.modalFilter .backdrop',function(e){
            const preventClose = $(this).closest('.modalFilter').hasClass("persist")
            if(!preventClose) self.close()
          })
          
          $(document).on('keyup','.modalFilter .filterSearch',function(e){
            if($(this).val().length > 0){
              if(e.keyCode === 13 || e.which === 13) {
                self.search.query($(this).val())
              }else{
                $('.enterAlert').addClass('on')
                self.search.allowClear.enable()
                self.clearSelection()
              }
            }else{
              // self.search.clear()
              self.search.allowClear.disable()
              $('.enterAlert').removeClass('on')
            }
          })

          $(document).on('click','.filterSearchClear',function(e){
            $(".modalFilter .filterSearch").val("")
            $('.enterAlert').removeClass('on')
            self.search.clear()
            self.search.allowClear.disable()
          })

          $(document).on('click','.filterSubmit',function(e){
            e.preventDefault()
            self.submit()
          })
          
          // $(document).on('click','.iCheck',function(e){
          //   e.preventDefault()
          // })

          self.options.eventsLoaded = true
        }
      },
      run: () => {
        const self = blustock.filter
        const runOnLoad = $('.modalFilter').hasClass('ini')

        if(runOnLoad) {
          setTimeout(() => {
            self.open()
          },500)
        }
        
        self.events()
      }
    },
    group: {
      select: (group) => {
        blustock.template.spinner.on()

        const data = { "group_id": group }

        axios.post('/selectgroup',  data)
        .then(function (response) {
            console.log("Selected Group: ", response.data);
            location.reload()
        })
        .catch(function (error) {
            console.log("error: ", error);
            blustock.template.spinner.off()
        })
        .then(function () {
        })
      }
    },
    events: () => {
      if(!blustock.options.eventsLoaded){

        $(document).on('click','.nolink', function(e){
          e.preventDefault()
        })
        
        $(document).on('change','#select_group_dropdown', function(e){
          const group = $(this).val()
          blustock.group.select(group)
        })

        $(document).on("focus",".labelController", (e) => {
          blustock.form.labelControl.add($(e.target))
        })

        $(document).on("blur",".labelController", (e) => {
          blustock.form.labelControl.remove($(e.target))
        })

        $(document).on("mousemove", (e) => {
          blustock.template.login.sidebar.track(e)
        })

        // $(document).on('collapsed.pushMenu expanded.pushMenu', function(e){
        //   blustock.sidebar.toggle.set(e.type)
        // });
        
        $(document).on("click", ".submenu-toggle", function(e) {
          e.preventDefault()
          var id = $(this).attr("sub-id")

          blustock.sidemenu.toggle(id)
          // blustock.template.login.sidebar.track(e)
        })

        $(document).on("keyup", (e) => {
          const key = e.which
          blustock.form.keyControl(key)
        })
        
        $(document).on("click", ".filterToggle", function(e) {
          const single = eval($(this).attr('filter-single'))

          if(typeof maps !== "undefined") maps.timer.clear()
          blustock.filter.open(single)
        })

        $(document).on("click", ".appModalToggle", function(e) {
          const controller = $(this).attr("modal-control")

          if(typeof controller === "undefined") console.error("Modal não identificada. Certifique-se de ter inserido a modal-controller")
          else blustock.template.modal.toggle(controller)
        })
        
        $(document).on("click", ".appModalBackdrop", function(e) {
          const modal = $(this).closest(".appModal")
          const controller = $(this).closest(".appModal").attr("modal-control")

          if(!modal.hasClass("persistent")) blustock.template.modal.close(controller)
        })

        // $(document).on("click", ".appLoader .backdrop", function(e) {
        //   blustock.template.spinner.off()
        // })

        var hoverTimout = null

        // $(document).on("mouseenter mouseleave", ".topBalloonControl", function(e) {
        //   const itemID = $(this).attr("menu-id")
        //   const double = $(this).hasClass("double")

        //   if(e.type === "mouseenter"){
        //     blustock.template.topmenu.hover(itemID,double)
        //   }
        //   if(e.type === "mouseleave"){
        //     blustock.template.topmenu.leave()
        //   }
        // })
        // $(document).on("mouseenter mouseleave", ".topMenuBalloon", function(e) {
        //   if(e.type === "mouseenter"){
        //     clearTimeout(blustock.template.topmenu.balloon.timeout)
        //   }
        //   if(e.type === "mouseleave"){
        //     blustock.template.topmenu.leave()
        //   }
        // })


        if($(".tableReorderControl").length > 0){
          $(".tableReorderControl").DataTable().on('column-reorder', function ( e, settings, details ) {
            const columns = settings.aoColumns
            var uri = settings.nTable.baseURI.split("/")
                uri = uri[uri.length-1]
            
            $(document).on("mouseup", function () {
              if(blustock.tools.dataTables.options.timeout !== null) clearTimeout(blustock.tools.dataTables.options.timeout)
              blustock.tools.dataTables.colReorder.set(uri,columns,details)
            })
          })
        }
        
        $(document).on("focus", ".validation-error", function(e) {
          // $(this).removeClass("validation-error")
        })
        $(document).on("mouseenter mouseleave", ".userSessionController", function(e) {
          if(e.type === "mouseenter") blustock.template.logout.show()
          if(e.type === "mouseleave") blustock.template.logout.hide()
        })
        
        $(document).on("click", ".btLogout", function(e) {
          $("#logout-form").submit()
        })

        
        blustock.options.eventsLoaded = true
      }
    },
    run: () => {
      if($('.loginPage').length > 0) blustock.template.login.start()
      
      blustock.form.checkbox.run()
      blustock.template.sidebar.run()
      blustock.template.collapse.run()
      // blustock.template.header.control()
      blustock.filter.run()
      blustock.events()
      // blustock.tools.dataTables.colReorder.set()
    }
  }

  blustock.run()

})