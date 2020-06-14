$(document).ready(function(){

  window.blustock = {
    state: {
      eventsLoaded: false
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
            if(!login.hasClass("out")) blustock.login.sidebar.move("on")
          }else{
            blustock.login.sidebar.move("out")
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
    sidebar:{
      toggle:{
        set: (state) => {
          if(state === "collapsed") $('.sidebar-toggle').addClass("collapsed");
          else $('.sidebar-toggle').removeClass("collapsed");
        }
      }
    },
    sidemenu: {
      options: {
        opened: false,
        timeout: null
      },
      toggle: (id) => {
        const self = blustock.sidemenu
        // console.log('toggle: ', blustock.sidemenu.opened)
        if(self.options.opened) {
          const opened = $(".submenu-menu.on").attr("sub-id")

          if(id === opened){
            self.close()
          }else{
            self.close(true)
            self.open(id,true)
          }
        }
        else self.open(id)
      },
      open: (id,keep) => {
        const self = blustock.sidemenu

        if(!keep || typeof keep === "undefined"){
          $('.submenu').addClass("open")
        } 
        
        $(".submenu-menu[sub-id='"+id+"']").addClass("on")
        self.options.opened = true

      },
      close: (hold) => {
        const self = blustock.sidemenu
        // console.log('sidemenu: ' + this.opened)
        if(!hold || typeof hold === "undefined"){
          $('.submenu').removeClass("open")
        } 
        $(".submenu-menu").removeClass("on")
        self.options.opened = false
      },
      events: () => {
        $(document).on('click',function(e){

        })
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
          blustock.checkbox.run()
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

        // console.log('options:' , options)
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
          // console.log('populate data:', data)
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

          blustock.checkbox.run()
          $('.treeView').removeClass('on')
        },
        clear: () => {
          blustock.filter.clearSelection()

          $(".searchResult .scrollView li").remove()
          $('.modalFilter .searchResult').removeClass('on')
          $('.treeView').addClass('on')
        },
        query: (q) => {
          // console.log('search: ', q)
          const self = blustock.filter.search

          self.progress.add()

          const searchParams = {
            string: q
          }
          axios.get('search',{
            params: searchParams
          })
          .then(function (response) {
            // handle success
            // console.log("response: ", response);
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

        blustock.spinner.on()

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

        // console.log('data:',data)
        const content = data.selecteds
        var unitsID = []

        for(var i = 0; i < content.length; i++){
          unitsID.push(content[i].id_unit)
        }
        
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
          blustock.spinner.off()
          self.close()
        });
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
          blustock.sidemenu.close()
        break;
      }
    },
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
    modal: {
      toggle: (controller) => {
        const self = blustock.modal

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
    dataTables:{
      colReorder:{
        set: () => {
            const isDataTable = ($(".tableReorderControl").length > 0 && $(".tableReorderControl").find("td").length > 0)
            console.log("isDataTable:", isDataTable)

            if(!isDataTable){
              setTimeout(() => {
                console.log("waited, now go...")
                blustock.dataTables.colReorder.set()
              },100)
              
            }else{
              setTimeout(() => {
                
              },400)
            }
            
        }
      }
    },
    events: () => {
      if(!blustock.state.eventsLoaded){
        $(document).on('click','.nolink', function(e){
          e.preventDefault()
        })
        $(document).on("focus",".labelController", (e) => {
          blustock.labelControl.add($(e.target))
        })

        $(document).on("blur",".labelController", (e) => {
          blustock.labelControl.remove($(e.target))
        })

        $(document).on("mousemove", (e) => {
          blustock.login.sidebar.track(e)
        })

        $(document).on('collapsed.pushMenu expanded.pushMenu', function(e){
          blustock.sidebar.toggle.set(e.type)
        });
        
        $(document).on("click", ".submenu-toggle", function(e) {
          e.preventDefault()
          var id = $(this).attr("sub-id")

          blustock.sidemenu.toggle(id)
          // blustock.login.sidebar.track(e)
        })

        $(document).on("keyup", (e) => {
          const key = e.which
          blustock.keyControl(key)
        })
        
        $(document).on("click", ".filterToggle", function(e) {
          const single = eval($(this).attr('filter-single'))

          if(typeof maps !== "undefined") maps.timer.clear()
          blustock.filter.open(single)
        })

        $(document).on("click", ".appModalToggle", function(e) {
          const controller = $(this).attr("modal-control")

          if(typeof controller === "undefined") console.error("Modal não identificada. Certifique-se de ter inserido a modal-controller")
          else blustock.modal.toggle(controller)
        })
        
        $(document).on("click", ".appModalBackdrop", function(e) {
          const modal = $(this).closest(".appModal")
          const controller = $(this).closest(".appModal").attr("modal-control")

          if(!modal.hasClass("persistent")) blustock.modal.close(controller)
        })

        // $(document).on("click", ".appLoader .backdrop", function(e) {
        //   blustock.spinner.off()
        // })

        if($(".tableReorderControl").length > 0){
          $(".tableReorderControl").DataTable().on( 'column-reorder', function ( e, settings, details ) {
            console.log("e",e)
            console.log("settings",settings)
            console.log("details",details)
          })
        }
        

        blustock.state.eventsLoaded = true
      }
    },
    run: () => {
      if($('.loginPage').length>0) blustock.login.start()
      
      // moment.locale('pt')
      blustock.checkbox.run()
      blustock.filter.run()
      blustock.events()
      // blustock.dataTables.colReorder.set()
    }
  }

  blustock.run()

})