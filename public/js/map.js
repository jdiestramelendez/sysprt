// const axios = require('axios');34
$(document).ready(function(){
  window.maps = {
    options: {
      eventsLoaded: false,
      keys:{
        app_id: 'tMQXxkXwtn26OPdBoVsB',
        app_code: 'wMKREx2eHdr1w0PDAIwZUQ',
        useCIT: true,
        useHTTPS: true
      },
      platform: null,
      map: null,
      behavior: null,
      ui: null,
      group: null,
      mGroup: null,
      cache: null,
      timeout: null,
      refreshLoop: 30,
      setViewBounds: true,
      dataPoints: [],
      cluster: null,
      clusteredDataProvider: null,
      runBubble: null,
      bubble: false,
      singleMarker: null,
      rPolyline: null,
      nightMode: false,
      fuso: null,
      country: null,
      ignition: {
        lowTime: 5,
        hightTime: 1440
      },
      addressValue: null,
      follow: {
        active: false,
        id: null
      },
      countries:{
        BRA: {
          center: {lat: -12.7,lng: -56.2},
          zoom: 5
        },
        PRT: {
          center: {lat: 39.3,lng: -8.2},
          zoom: 7
        }
      }
    },
    config: (start) => {

      axios.get('/get_selected_group')
      .then(response => {
          console.log("response: ", response)
          maps.options.country = response.data.selected_group.country

          //GET CONFIG
          axios.get('getmapconfig')
          .then(function (response) {

            console.log("response: ", response.data);

            // handle success
            maps.options.ignition.lowTime = Number(response.data.pos_low_time)
            maps.options.ignition.hightTime = Number(response.data.pos_high_time)
            maps.options.fuso = response.data.carbon_fuso_name

            maps.start()
            maps.events()

            console.log("ignition: ", maps.options.ignition)
            console.log("fuso: ", maps.options.fuso)
          })
          .catch(function (error) {
            // handle error
            console.log("error: ", error);
          })
          .then(function () {
            // always executed
            // self.progress.remove()
          });
      })
      .catch(error => {
        console.error("error: ", error)
          
      })
      .then(() => {
          
          // blustock.template.spinner.off()
      })

      
    },
    start: () => {
      const platform = new H.service.Platform(maps.options.keys)
      // var pixelRatio = window.devicePixelRatio || 1;
      var defaultLayers = platform.createDefaultLayers({
        lg: 'POR',
        // tileSize: pixelRatio === 1 ? 256 : 512,
        // ppi: pixelRatio === 1 ? undefined : 320
      });
  
      const map = new H.Map(document.getElementById('map'),
      // defaultLayers.normal.map, {pixelRatio: pixelRatio});

      defaultLayers.normal.map, {
        zoom: maps.options.countries[maps.options.country].zoom,
        center: maps.options.countries[maps.options.country].center
      })
      
      const behavior = new H.mapevents.Behavior(new H.mapevents.MapEvents(map))
      var ui = H.ui.UI.createDefault(map, defaultLayers, 'pt-BR')
      const group = new H.map.Group()
      map.addObject(group)

      var mapSettings = ui.getControl('mapsettings')
      var zoom = ui.getControl('zoom')
      var scalebar = ui.getControl('scalebar')
      // var panorama = ui.getControl('panorama').setEnabled(true)

      // panorama.setAlignment('top-left')
      mapSettings.setAlignment('top-left')
      zoom.setAlignment('top-left')
      scalebar.setAlignment('top-left')

      maps.options.platform = platform
      maps.options.map = map
      maps.options.behavior = behavior
      maps.options.ui = ui
      maps.options.group = group

    },
    marker: {
      add: (options,info) => {
        // console.log('options',options)
        const marker = new H.map.DomMarker(
          {
            lat: options.lat,
            lng: options.lng
          }
          // { icon: domIcon }
        )

        const dropOptions = {
          options: options,
          info: info
        }
        
        maps.options.dataPoints.push(new H.clustering.DataPoint(options.lat, options.lng, null, options));
      },
      addSingle: (lat,lng) => {
        const map = maps.options.map
        const coords = {lat: lat, lng: lng}

        if(maps.options.singleMarker !== null) {
          map.removeObjects([maps.options.singleMarker])
          maps.options.singleMarker = null
        }
        maps.options.singleMarker = new H.map.Marker(coords)

        map.addObject(maps.options.singleMarker)
        map.setCenter(coords)
        map.setZoom(18)
      },
      addToGroup: (group, coordinate, html) => {
        const marker = new H.map.Marker(coordinate);
        // add custom data to the marker
        marker.setData(html);
        maps.options.group.addObject(marker)
      },
      infoBubble:{
        add: (data) => {
          const self = maps.marker.infoBubble

          var position = e.target.getPosition()
          var data = e.target.getData()

          var bubbleContent = self.assemble(data, position)
          var bubble = onMarkerClick.bubble          
          
          if (!bubble) {
            bubble = new H.ui.InfoBubble(position, {
              content: bubbleContent
            })
            ui.addBubble(bubble)
            onMarkerClick.bubble = bubble
          } else {
            bubble.setPosition(position)
            bubble.setContent(bubbleContent)
            bubble.open()
          }

          map.setCenter(position, true);
        },
        assemble: (data,position) => {
          
          const dropPos = [position.lat,position.lng].toString()
          
          console.log('data: ',data)
          // console.log(maps.options.adressValue)
          const balloon = $(".mapBalloon")
          if(balloon.length > 0) balloon.remove()
          
          const info = "<div class='balloonHeader'>" + data.registration + "</div>" +
                        "<div class=''> <label>Veículo: </label> " + data.description + "</div>" +
                        "<div class=''> <label>Motorista: </label> " + data.lastdriver + "</div>" +
                        "<div class=''> <label>Velocidade: </label> " + data.speed + " km/h</div>" +
                        "<div class=''> <label>Endereço: </label> <span class='address'>...</span></div>" +
                        "<div class=''> <label>Data: </label> " + moment(data.date,"YYYY-MM-DD HH:mm:ss.SSS").format("DD/MM/YYYY - HH:mm:ss") + "</div>" +
                        "<div class=''> <label>Traçar rota: </label>" +
                        "<div class='routingActions'><a href='#' class='nolink fromHere' pos-control='" + dropPos +"'>a partir daqui</a>" +
                        "<a href='#' class='nolink toHere' pos-control='" + dropPos +"'>até daqui</a></div>" +
                        "<div class='routingActions'><a href='#' class='nolink follow' pos-control='" + dropPos +"' description='" + data.description + "' unit-control='" + data.unitID + "'>Seguir</a></div>" +
                        "</div>"
          
          maps.routes.getAddressFromPosition([data.lat, data.lng], '' , 'getAddress')

          return info
        }
      },
      cluster: {
        set: () => {
          const map = maps.options.map
          const ui = maps.options.ui
          var group = maps.options.group

          const theme = {
            getClusterPresentation: function(cluster) {
              
              var clusterSvgTemplate = '<svg version="1.1" xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" viewBox="0 0 40 40" style="enable-background:new 0 0 40 40;" xml:space="preserve">' +
                                       '<g><circle fill="{fillColor}" cx="20" cy="20" r="15"/></g>' + 
                                       '<g style="opacity: .3"><circle fill="{fillColor}" cx="20" cy="20" r="20"/></g>' +
                                       '<text width="50" y="24" x="{labelX}" style="fill: #FFFFFF; font-size: 11px; font-weight: bold; text-align: center; font-family: {fonts}" transform="translateY(-50%)">{weight}</text>' +
                                       '</svg>'

              const totalItems = cluster.g.a.length
              var clusterMarker
              var fillColor = "#1aa4f3"
              // console.log("totalItems: ",totalItems)

              const weight = cluster.getWeight()
              const fonts = "'Source Sans Pro','Helvetica Neue',Helvetica,Arial,sans-serif"
              const wl = weight.toString().length
              const labelX = (40 - (wl * 6)) / 2
              const percent = Math.floor((weight * 100) / totalItems)
                    
              svgString = clusterSvgTemplate.replace(/\{fillColor\}/g, fillColor)
                                            .replace(/\{weight\}/g, weight)
                                            .replace(/\{fonts\}/g, fonts)
                                            .replace(/\{labelX\}/g, labelX)                                 

              clusterIcon = new H.map.Icon(svgString, {
                size: {w: 40, h: 40},
                anchor: {x: 20, y: 20}
              }),
          
              clusterMarker = new H.map.Marker(cluster.getPosition(), {
                icon: clusterIcon,
                min: cluster.getMinZoom(),
                max: cluster.getMaxZoom()
              });

              clusterMarker.setData(cluster)
              
              return clusterMarker
            },
            getNoisePresentation: function (noisePoint) {
              const data = noisePoint.getData()
              const iconW = 54
              const iconH = 36
              // console.log("data: ",data)
              var iconTemplate = '<svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 54 36" style="enable-background:new 0 0 54 36;" xml:space="preserve"><g>' +
                                 '<path fill="#FFFFFF" d="M41.08,8.01h-6.96c-0.07-3.87-3.22-6.98-7.11-6.98c-3.88,0-7.03,3.11-7.11,6.98h-6.96' +
                                 'c-6.58,0-11.94,5.35-11.94,11.94s5.35,11.94,11.94,11.94h10.86l2.47,2.76c0.39,0.44,1.07,0.44,1.46,0l2.47-2.76h10.86' +
                                 'c6.58,0,11.94-5.35,11.94-11.94S47.67,8.01,41.08,8.01z"/> <g>' +
                                 '<path d="M30.39,5.8H30.1V5.28c0-0.56-0.46-1.02-1.02-1.02h-4.11c-0.56,0-1.02,0.46-1.02,1.02V5.8h-0.29' +
                                 'c-0.12,0-0.21,0.09-0.21,0.21v1.17c0,0.12,0.09,0.21,0.21,0.21h0.29v2.53c-0.11,0.1-0.18,0.25-0.18,0.41v0.39' +
                                 'c0,0.31,0.25,0.56,0.56,0.56h0.07v0.56c0,0.09,0.07,0.16,0.16,0.16h0.75c0.09,0,0.16-0.07,0.16-0.16v-0.56h3.11v0.56' +
                                 'c0,0.09,0.07,0.16,0.16,0.16h0.75c0.09,0,0.16-0.07,0.16-0.16v-0.56h0.07c0.31,0,0.56-0.25,0.56-0.56v-0.39' +
                                 'c0-0.16-0.07-0.31-0.18-0.41V7.4h0.29c0.12,0,0.21-0.09,0.21-0.21V6.01C30.6,5.9,30.5,5.8,30.39,5.8L30.39,5.8z M29.47,5.89v2.51' +
                                 'H27.3V5.89H29.47z M25.94,4.8h2.16c0.15,0,0.27,0.12,0.27,0.27c0,0.15-0.12,0.27-0.27,0.27h-2.16c-0.15,0-0.27-0.12-0.27-0.27' +
                                 'C25.67,4.92,25.79,4.8,25.94,4.8L25.94,4.8z M25.55,9.51c0,0.15-0.12,0.27-0.27,0.27h-0.44c-0.15,0-0.27-0.12-0.27-0.27v-0.3' +
                                 'c0-0.15,0.12-0.27,0.27-0.27h0.44c0.15,0,0.27,0.12,0.27,0.27V9.51z M28.5,9.51v-0.3c0-0.15,0.12-0.27,0.27-0.27h0.44' +
                                 'c0.15,0,0.27,0.12,0.27,0.27v0.3c0,0.15-0.12,0.27-0.27,0.27h-0.44C28.62,9.78,28.5,9.66,28.5,9.51L28.5,9.51z M28.5,9.51' +
                                 'M24.58,5.89h2.17v2.51h-2.17V5.89z"/> </g> <g>' +
                                 '<path fill="{ignition}" d="M41.08,29.26H12.96c-5.15,0-9.32-4.17-9.32-9.32v0c0-1.81,0.51-3.49,1.4-4.92c1.8-2.9,5.15-4.38,8.49-4.1' +
                                 'c0.98,0.08,1.92,0.42,2.79,0.88l5.03,2.63c3.57,1.87,7.84,1.86,11.41-0.01l4.93-2.59c1.05-0.55,2.21-0.91,3.4-0.94' +
                                 'c2.91-0.08,5.76,1.16,7.51,3.55c1.13,1.54,1.8,3.45,1.8,5.5v0C50.4,25.09,46.23,29.26,41.08,29.26z"/> </g>' +
                                 '<path style="opacity: .1" d="M27.02,1.03c3.88,0,7.03,3.11,7.11,6.98h6.96c6.58,0,11.94,5.35,11.94,11.94s-5.35,11.94-11.94,11.94H30.22' +
                                 'l-2.47,2.76c-0.19,0.22-0.46,0.33-0.73,0.33s-0.54-0.11-0.73-0.33l-2.47-2.76H12.96c-6.58,0-11.94-5.35-11.94-11.94' +
                                 'S6.38,8.01,12.96,8.01h6.96C19.99,4.14,23.14,1.03,27.02,1.03 M27.02,0.03c-4.06,0-7.47,3.05-8.03,6.98h-6.04' +
                                 'c-7.13,0-12.94,5.8-12.94,12.94c0,7.13,5.8,12.94,12.94,12.94h10.41l2.17,2.43c0.38,0.42,0.91,0.66,1.48,0.66' +
                                 'c0.56,0,1.1-0.24,1.48-0.66l2.17-2.43h10.41c7.13,0,12.94-5.8,12.94-12.94c0-7.13-5.8-12.94-12.94-12.94h-6.04' +
                                 'C34.49,3.08,31.08,0.03,27.02,0.03L27.02,0.03z"/>' +
                                 '</g> <text transform="translate({labelX},25)" fill="{textColor}" style="font-size: 9px; font-weight: normal; text-align: center; font-family: {fonts}">{registration}</text> </svg>'


              // console.log("icon raw:", iconTemplate)

              var date = data.date
                  date = date.split(" ")[0] + " " + date.split(" ")[1]
                  date = moment(date,"YYYY-MM-DD HH:mm:ss.SSS")
                  // console.log("date: ",date)
                  
               var now = moment().utc().tz(maps.options.fuso).format("DD/MM/YYYY - HH:mm:ss")
               var useNow = moment(now, "DD/MM/YYYY - HH:mm:ss")
               var hasBeen = useNow.diff(date)
                  
                  hasBeen = moment.duration(hasBeen).asMinutes()
                  // console.log("hasBeen: ",hasBeen)
                  // console.log("hasBeenHours: ",hasBeenHours)

              var ignition = (data.ignitionOn ? "#c6e02d" : "#ef3e4a")
                  // console.log("ignition 1: ",ignition)

                  if(hasBeen >= maps.options.ignition.lowTime){
                    ignition = (hasBeen >= maps.options.ignition.hightTime ? "#cfcfcf" : "#ffc41f")
                    // console.log("ignition hasBeen: ",ignition)
                  }

              const fonts = "'Andale','Source Sans Pro','Helvetica Neue',Helvetica,Arial,sans-serif"
              const labelLength = data.registration.length
              // console.log("registration: "+ data.registration +": "+ labelLength)
              const labelX = (iconW - (labelLength * 4.8)) / 2
              const textColor = (ignition === "#ef3e4a" ? "#ffe8ea" : "#2d2d2d")

              const icon = iconTemplate.replace(/\{ignition\}/g, ignition)
                                       .replace(/\{registration\}/g, data.registration)
                                       .replace(/\{fonts\}/g, fonts)
                                       .replace(/\{labelX\}/g, labelX)
                                       .replace(/\{textColor\}/g, textColor)
              
            // console.log("icon:", icon)

              var noiseMarker = new H.map.Marker(noisePoint.getPosition(), {
                min: noisePoint.getMinZoom(),
                icon: new H.map.Icon(icon, {
                  size: {w: iconW, h: iconH},
                  anchor: {x: (iconW / 2), y: iconH}
                })
              })

            noiseMarker.setData(data)
            group.addObjects([noiseMarker])

            return noiseMarker
            }
          }

          var clusteredDataProvider = new H.clustering.Provider(maps.options.dataPoints, {
            clusteringOptions: {
              eps: 10,
              minWeight: 3
            },
            strategy: "DYNAMICGRID",
            // min: 6,
            // max: 20,
            theme: theme
          })

          var bubble = ""

          maps.options.runBubble = function (e) {
            if(typeof e === "undefined"){
              bubble = ""
              $(".mapBalloon").remove()
            }else{
              if(e.target instanceof H.map.Marker || e.target instanceof H.map.DomMarker){
                bubble = ""
                $(".mapBalloon").remove()
  
                const data = e.target.getData()
                const isCluster = typeof data.registration === "undefined"
                const position = e.target.getPosition()
                // console.log("isCluster",isCluster)
  
                if(!isCluster){
                  bubble =  new H.ui.InfoBubble(position, {
                    content: maps.marker.infoBubble.assemble(data,position)
                  })
  
                  bubble.addClass('mapBalloon')
                  ui.addBubble(bubble)
                }
              }else{
                bubble = ""
                $(".mapBalloon").remove()
              }
            }
            
          }
          
          maps.options.cluster = new H.map.layer.ObjectLayer(clusteredDataProvider)
          
          map.addObject(group)
          map.addLayer(maps.options.cluster)

          var followActive = maps.options.follow.active
          var pos

          if(followActive){
            var curr = maps.options.dataPoints.filter(item => {
              return item.data.unitID == maps.options.follow.id
            })

            pos = {
              lat: curr[0].lat,
              lng: curr[0].lng
            }
          }else{
            pos = {
              lat: maps.options.dataPoints[0].lat,
              lng: maps.options.dataPoints[0].lng
            }

            map.setZoom(10)
          }
          
          map.setCenter(pos)
          if(maps.options.runBubble !== null) maps.options.runBubble()    
        }
      }
    },
    clear: () => {
      maps.options.group.removeAll()
      if(maps.options.runBubble !== null) maps.options.map.removeEventListener('pointermove',maps.options.runBubble)
      maps.options.map.removeLayer(maps.options.cluster)
    },
    refresh: () => {
      // blustock.spinner.on()
      maps.timer.clear()
      setTimeout(() => {
        blustock.filter.getPositions("",false)
      },100)
    },
    timer:{
      control: () => {
        var countToLoop = 0
        maps.timer.clear()

        maps.options.timeout = setInterval(() => {
          if(countToLoop === maps.options.refreshLoop){
            maps.timer.clear()

            maps.refresh()
          }else{
            $(".updatedAt").text(maps.options.refreshLoop-countToLoop)
            countToLoop++
          }
        },1000)
      },
      clear: () => {
        clearInterval(maps.options.timeout)
        maps.options.timeout = null
        maps.options.dataPoints = []
        maps.options.dataPoints.length = 0
        $(".updatedAt").text(0)
      }
    },
    positions: {
      addAssets: (data) => {
        maps.clear()

        for(var i = 0; i < data.length; i++){
          if(data[i] === null) console.log('item vazio')
          else{
            var options = {
              lat: data[i].Latitude,
              lng: data[i].Longitude,
              date: data[i].Timestamp,
              unitID: data[i].UnitId,
              lastdriver: '',
              description: data[i].assets.description,
              registration: data[i].assets.registration_number,
              vehicleType: 'bus',
              speed: data[i].SpeedKilometresPerHour,
              ignitionOn: (data[i].IgnitionOn === 1 ? true : false)
            }

            if(data[i].driver !== null){
              options.lastdriver = data[i].driver.name
            }
            
            const info = "<div class='balloonHeader'>" + options.registration + "</div>" +
                         "<div class=''> <label>Veículo: </label> " + options.description + "</div>" +
                         "<div class=''> <label>Motorista: </label> " + options.lastdriver + "</div>" +
                         "<div class=''> <label>Data: </label> " + moment(options.date,"YYYY-MM-DD HH:mm:ss.SSS").format("DD/MM/YYYY - HH:mm:ss") + "</div>"            
                         
            maps.marker.add(options,info)
          }
        }

        maps.marker.cluster.set()
        maps.timer.control()
        blustock.template.spinner.off()
      }
    },
    routes:{
      options: {
        from: [],
        to: [],
      },
      autocomplete: {
        search: (query,type) => {
          const self = maps.routes.autocomplete

          var params = '?' +
            'query=' +  encodeURIComponent(query) +
            '&beginHighlight=' + encodeURIComponent('<mark>') + 
            '&endHighlight=' + encodeURIComponent('</mark>') + 
            '&maxresults=5' +
            '&app_id=' + maps.options.keys.app_id +
            '&app_code=' + maps.options.keys.app_code

          axios.get('https://autocomplete.geocoder.api.here.com/6.2/suggest.json' + params)
            .then(function (response) {
                const suggestions = response.data.suggestions
                self.drop(suggestions,type)
            })
            .catch(function (error) {
                console.log("error: ", error);
            })
            .then(function () {

            });
        },
        drop: (suggestions,type) => {
          $(".autocompleteControl").removeClass("on").find("ul").empty()

          var drop = ""

          for(var i = 0; i < suggestions.length; i++){
            const c = ", "
            const h = "-"

            var street = suggestions[i].address.street
                street = (typeof street === "undefined" || street === "" ? "" : street + c)
            var houseNumber = suggestions[i].address.houseNumber
                houseNumber = (typeof houseNumber === "undefined" || houseNumber === "" ? "" : houseNumber + c)
            var district = suggestions[i].address.district
                district = (typeof district === "undefined" || district === "" ? "" : district + c)
            var city = suggestions[i].address.city
                city = (typeof city === "undefined" || city === "" ? "" : city + c)
            var state = suggestions[i].address.state
                state = (typeof state === "undefined" || state === "" ? "" : state + c)
            var postalCode = suggestions[i].address.postalCode
                postalCode = (typeof postalCode === "undefined" || postalCode === "" ? "" : postalCode)
            

            const address = street + houseNumber + district + city + state + postalCode

            drop += "<li class='autocompleteSelector' ac-label='" + suggestions[i].label + "'>" + address + "</li>"
          }

          $(".autocompleteControl[ac-control='" + type + "']").addClass("on").find("ul").append(drop)
        },
        select: (controller,address) => {
          maps.routes.searchFromAddress(address,controller)

          // console.log("controller: ",controller)
          var input

          if(controller === "from") input = $(".fromHereInput")
          if(controller === "to") input = $(".toHereInput")
          if(controller === "address") input = $(".addressSearch")

          // const input = (controller === "from" ? $(".fromHereInput") : $(".toHereInput"))
          input.val(address)

          $(".autocompleteControl").removeClass("on").find("ul").empty()
        }
      },
      setFrom: (from) => {
        $(".fromHere").addClass("on")
        $(".toHere").removeClass("on")
        maps.locationSearch.on()
        maps.routes.getAddressFromPosition(from,'from')
      },
      setTo: (to) => {
        $(".fromHere").removeClass("on")
        $(".toHere").addClass("on")
        maps.locationSearch.on()
        maps.routes.getAddressFromPosition(to,'to')
      },
      searchAddress: (latLng) => {
        // console.log("latLng: ",latLng)

      },
      searchFromAddress: (query,controller) => {
        const platform = maps.options.platform
        
        var geocoder = platform.getGeocodingService()
        var geocodingParameters = {
          searchText: query,
          jsonattributes : 1
        }

        geocoder.geocode(
          geocodingParameters,
          onSuccess,
          onError
        )

        function onSuccess (response) {
          if(typeof controller !== "undefined"){
            if(response.response.view.length > 0){
              
              var target, input

              if(controller === "from") {
                target = $("#fromHere")
                input = $(".fromHere")
              }
              if(controller === "to") {
                target = $("#toHere")
                input = $(".toHere")
              }
              if(controller === "address") {
                target = $("#addressSearch")
                input = $(".addressSearch")
              }

              const position = response.response.view[0].result[0].location.displayPosition
              const latLng = position.latitude + ',' + position.longitude

              target.val(latLng)

              const callback = input.attr("ac-callback")
              if(typeof callback !== "undefined") {
                eval(callback + "('" + position.latitude +"','"+ position.longitude + "')")
              }
            }
          }
        }
        function onError (error) {

        }

      },
      fromAtoB: (waypoints) => {
        const platform = maps.options.platform
        const routeType = "fastest"
        const vehicleType = "car"
        const mode = routeType + ";" + vehicleType
        var from, to
        var allow

        if(typeof waypoints === "undefined"){
          from = $("#fromHere").val()
          to = $("#toHere").val()

          allow = (from !== "" && to !== "" ? true : false)
          waypoints = {}
        }

        if(allow){
          waypoints.a = "geo!" + from
          waypoints.b = "geo!" + to

          // console.log("waypoints: ", waypoints)
          $(".btTracarRota").addClass("off").find(".btnSpinner").addClass("on")
  
          var router = platform.getRoutingService(),
            routeRequestParams = {
              mode: mode,
              representation: 'display',
              waypoint0: waypoints.a,
              waypoint1: waypoints.b,
              instructionformat: 'html',
              routeattributes: 'waypoints,summary,shape,legs',
              maneuverattributes: 'direction,action',
              jsonattributes: 1,
              language: "pt-br",
              alternatives: 1
            }
  
          router.calculateRoute(
            routeRequestParams,
            maps.routes.onSuccess,
            maps.routes.onError
          )
        }else{
          if(from === "") {
            $(".fromHereInput").addClass("validation-error")
          }else{
            if(to === "") $(".toHereInput").addClass("validation-error")
          }
        }
        
      },
      onSuccess: (result) => {
        // console.log('success result: ',result)
        const subtype = result.subtype
        if(subtype === "NoRouteFound" || typeof result === "undefine" || result.length === 0){
          alert("no route found")
        }else{
          var route = result.response.route[0]
          $(".mapBalloon").remove()

          maps.routes.addRouteShapeToMap(route)
          maps.routes.addManueversToMap(route)
          maps.routes.addSummaryToPanel(route.summary)
          maps.routes.addManueversToPanel(route)

          $(".btTracarRota").removeClass("off").find(".btnSpinner").removeClass("on")
        }
      },
      onError: (error) => {
        alert('Ooops!')
        $(".btTracarRota").removeClass("off").find(".btnSpinner").removeClass("on")
      },
      openBubble: (position, text) => {
        const ui = maps.options.ui
        var bubble = maps.options.bubble

        if (!bubble) {
          bubble = new H.ui.InfoBubble(
            position,
            // The FO property holds the province name.
            { content: text });
          ui.addBubble(bubble);
        } else {
          bubble.setPosition(position);
          bubble.setContent(text);
          bubble.open();
        }
      },
      addRouteShapeToMap: (route) => {
        const map = maps.options.map

        var strip = new H.geo.Strip(),
        routeShape = route.shape, polyline

        routeShape.forEach(function (point) {
          var parts = point.split(',')
          strip.pushLatLngAlt(parts[0], parts[1])
        });

        polyline = new H.map.Polyline(strip, {
          style: {
            lineWidth: 4,
            strokeColor: 'rgba(0, 128, 255, 0.7)'
          }
        })

        map.addObject(polyline)
        map.setViewBounds(polyline.getBounds(), true)

        maps.options.rPolyline = polyline
      },
      addManueversToMap: (route) => {
        const svgMarkup = '<svg width="18" height="18" ' +
                        'xmlns="http://www.w3.org/2000/svg">' +
                        '<circle cx="8" cy="8" r="8" ' +
                        'fill="#1b468d" stroke="white" stroke-width="1"  />' +
                        '</svg>'

        const map = maps.options.map
        var dotIcon = new H.map.Icon(svgMarkup, { anchor: { x: 8, y: 8 } })
        var group = new H.map.Group()

        // Add a marker for each maneuver
        for (var i = 0; i < route.leg.length; i += 1) {
          for (var j = 0; j < route.leg[i].maneuver.length; j += 1) {
            // Get the next maneuver.
            maneuver = route.leg[i].maneuver[j];
            // Add a marker to the maneuvers group
            var marker = new H.map.Marker(
              { lat: maneuver.position.latitude, lng: maneuver.position.longitude },
              { icon: dotIcon }
            )
            marker.instruction = maneuver.instruction
            group.addObject(marker)
          }
        }

        group.addEventListener('tap', function (evt) {
          map.setCenter(evt.target.getPosition())
          maps.routes.openBubble(
            evt.target.getPosition(), evt.target.instruction)
        }, false)

        map.addObject(group)

        maps.options.mGroup = group
      },
      addManueversToPanel: (route) => {
        var nodeOl = $(".routeStepsController>ol")
        var item

        // Add a marker for each maneuver
        for (var i = 0; i < route.leg.length; i += 1) {
          for (var j = 0; j < route.leg[i].maneuver.length; j += 1) {
            maneuver = route.leg[i].maneuver[j]

            item = "<li class=''><span class='arrow " + maneuver.action + "'></span>" + 
                   "<p>" + (j + 1) + ". " + maneuver.instruction + "</p>" + 
                   "</li>"

            nodeOl.append(item);
          }
        }
      },
      addSummaryToPanel: (summary) => {
        const node = $(".routeStepsController")
        const nodeOl = node.find("ol")
        nodeOl.empty()
        moment.locale("pt-br")
        var trafficTime = moment.duration(summary.trafficTime,"seconds")

        var rawDist = Number(summary.distance)
        // console.log("rawDist: ",rawDist)

        var distance = (Number(summary.distance) >= 1000 ? (summary.distance/1000).toFixed(1) + " km" : summary.distance + " m")
            distance = distance.replace(".",",") 
          // console.log("distance: ",distance)
        trafficTime =  trafficTime.humanize()

        const item = "<li><i class='fas fa-arrows-alt-h'></i> " + distance + "</br>" +
                     "<i class='fas fa-clock'></i> " + trafficTime + "</li>"


        nodeOl.append(item);
        node.addClass("on")

        moment.locale("en")
      },
      dropEnderecoToBubble: (position) => {
        platform = maps.options.platform
        console.log('position: ',position.toString())

        var geocoder = platform.getGeocodingService(),
          reverseGeocodingParameters = {
            prox: position,
            mode: 'retrieveAddresses',
            maxresults: '1',
            jsonattributes : 1
          };
        
        
          geocoder.reverseGeocode(
            reverseGeocodingParameters,
            onSuccess,
            onError
          )
          
          // maps.options.addressValue = locations[0].location.address.label
        function onSuccess (result){
          const locations = result.response.view[0].result
          const lat = locations[0].location.displayPosition.latitude
          const lng = locations[0].location.displayPosition.longitude
          const latLng = lat + ',' + lng

            const target = (type === "from" ? $(".fromHereInput") : $(".toHereInput"))
            const iTarget = (type === "from" ? $("#fromHere") : $("#toHere"))
            
            target.val(locations[0].location.address.label)
            iTarget.val(latLng)

            // $(".collapse-body[item-id='rota']").closest(".collapse-item").addClass("on")
            blustock.template.collapse.open("rota")  
        }

        function onError (error) {

        }
      },
      getAddressFromPosition: (position,type,callback) => {
        platform = maps.options.platform
        console.log('position: ',position.toString())

        var geocoder = platform.getGeocodingService(),
          reverseGeocodingParameters = {
            prox: position,
            mode: 'retrieveAddresses',
            maxresults: '1',
            jsonattributes : 1
          };
        
          
          geocoder.reverseGeocode(
            reverseGeocodingParameters,
            onSuccess,
            onError
          )
          
          // maps.options.addressValue = locations[0].location.address.label
        function onSuccess (result){
          const locations = result.response.view[0].result
          const lat = locations[0].location.displayPosition.latitude
          const lng = locations[0].location.displayPosition.longitude
          const latLng = lat + ',' + lng

            const target = (type === "from" ? $(".fromHereInput") : $(".toHereInput"))
            const iTarget = (type === "from" ? $("#fromHere") : $("#toHere"))
            
            target.val(locations[0].location.address.label)
            iTarget.val(latLng)

            // $(".collapse-body[item-id='rota']").closest(".collapse-item").addClass("on")
            blustock.template.collapse.open("rota")

            if(typeof callback !== "undefined"){
              $('.address').text(locations[0].location.address.label)
            }
        }

        function onError (error) {

        }
      },
      clear: () => {
        $(".toHereInput").val("")
        $(".fromHereInput").val("")
        $("#toHere").val("")
        $("#fromHere").val("")

        $(".routeStepsController").removeClass("on").find("ol").empty()

        const map = maps.options.map

        map.removeObject(maps.options.rPolyline)
        map.removeObject(maps.options.mGroup)
      },
    },
    whereAmI: {
      getLocation: () => {
        
        if (navigator.geolocation) {
          navigator.geolocation.getCurrentPosition(maps.whereAmI.showPosition)
          blustock.template.spinner.on()
        } else {
          x.innerHTML = "Seu navegador não suporta geolocalização"
        }
      },
      showPosition: (position) => {
        blustock.template.spinner.off()
        const lat = position.coords.latitude
        const lng = position.coords.longitude

        maps.marker.addSingle(lat,lng)
        // console.log("position: ",position)
      }
    },
    nightMode: {
      toggle: () => {
        const self = maps.nightMode

        if(maps.options.nightMode) self.off()
        else self.on()
      },
      on: () => {
        const map = maps.options.map
        const platform = maps.options.platform
        // const nightMode = new H.map.layer.TileLayer("mapnight")
        var mapTileService = platform.getMapTileService({
          type: 'base'
        })

        var tileLayer = mapTileService.createTileLayer(
          // 'trucktile',
          'maptile',
          'normal.night',
          256,
          'png8',
          {
            pois: true
          }
        )
        map.setBaseLayer(tileLayer)

        maps.options.nightMode = true
        $(".mapModeToggle .dayIcon").removeClass("off")
        $(".mapModeToggle .nightIcon").addClass("off")
      },
      off: () => {
        const map = maps.options.map
        const platform = maps.options.platform
        var mapTileService = platform.getMapTileService({
          type: 'base'
        })

        var tileLayer = mapTileService.createTileLayer(
          // 'trucktile',
          'maptile',
          'normal.day',
          256,
          'png8',
          {
            pois: true
          }
        )
        map.setBaseLayer(tileLayer)

        maps.options.nightMode = false
        $(".mapModeToggle .dayIcon").addClass("off")
        $(".mapModeToggle .nightIcon").removeClass("off")
      },
      track: (e) => {
        const scheme = e.newValue.f.scheme_
        // console.log("scheme: ",scheme)
        
        if(scheme === "normal.day"){
          // console.log("night, turn to day")
          $(".mapModeToggle .dayIcon").addClass("off")
          $(".mapModeToggle .nightIcon").removeClass("off")
        }else{
          // console.log("day, turn to night")
          $(".mapModeToggle .dayIcon").addClass("off")
          $(".mapModeToggle .nightIcon").removeClass("off")
        }
        // console.log("scheme: ", scheme)
      }
    },
    contextMenu: {
      toggle: (e) => {
        // console.log("e: ",e)        
        const map = maps.options.map
        const coord = map.screenToGeo(e.viewportX, e.viewportY)
        const position = coord.lat+","+coord.lng

        const from = new H.util.ContextItem({
          label: 'Rota a partir daqui',
          callback: function(e) {
            maps.routes.setFrom(position)
          }
        })
        const to = new H.util.ContextItem({
          label: 'Rota até aqui',
          callback: function() {
            maps.routes.setTo(position)
          }
        })

        e.items.push(from,to)
      }
    },
    locationSearch: {
        toggle: () => {
          const isOn = $(".mapMenu").hasClass("on")
          const self = maps.locationSearch

          if(isOn) self.off()
          else self.on()
          
        },
        on: () => {
          const isOn = $(".mapMenu").hasClass("on")
          if(!isOn){
            $(".mapMenu").addClass("on")
          }
        },
        off: () => {
          $(".mapMenu").removeClass("on")
        }
    },
    resize: () => {
      const map = maps.options.map
      map.getViewPort().resize()
    },
    follow: {
      start: (id, description, pos) =>{
        // console.log("follow: ", id)
        $(".stopFollow").addClass("opened").find("span>span").text(description)
        
        maps.options.follow = {
          active: true,
          id: id
        }
        // console.log("pos: ", pos)
        let splitPos = pos.split(",")
        let dropPos = {
          lat: splitPos[0],
          lng: splitPos[1]
        }
        maps.options.map.setCenter(dropPos)
        if(maps.options.runBubble !== null) maps.options.runBubble()
      },
      end: () =>{
        console.log("stop follow: ")
        $(".stopFollow").removeClass("opened").find("span>span").text("")

        maps.options.follow = {
          active: false,
          id: null
        }
      }
    },
    events: () => {
      const map = maps.options.map
      
      if(!maps.options.eventsLoaded){
        window.addEventListener('resize',function(e) {
          map.getViewPort().resize()
        })

        $(document).on('collapsed.pushMenu expanded.pushMenu', function(e){
          setTimeout(() => {
            map.getViewPort().resize()
          },500)
        })

        map.addEventListener('contextmenu', function(e) {
          // console.log("context e:",e)
          maps.contextMenu.toggle(e)
        })

        map.addEventListener('tap', function(e) {
          if(maps.options.runBubble !== null) maps.options.runBubble(e)
        })
        
        map.addEventListener('baselayerchange', function(e) {
          maps.nightMode.track(e)
        })

        $(document).on('click','.mapRefresh',function(e){
          maps.refresh()
        })

        $(document).on('click','.fromHere',function(e){
          const pos = $(this).attr("pos-control")
          maps.routes.setFrom(pos)
        })
        $(document).on('keyup','.fromHereInput',function(e){
          const val = $(this).val()
          if(val.length >= 3) maps.routes.autocomplete.search(val,'from')
        })
        $(document).on('blur','.fromHereInput,.toHereInput',function(e){
          setTimeout(() => {
            $(".autocompleteControl").removeClass("on").find("ul").empty()
          },300)
        })
        $(document).on('click','.toHere',function(e){
          const pos = $(this).attr("pos-control")
          maps.routes.setTo(pos)
        })
        $(document).on('keyup','.toHereInput',function(e){
          const val = $(this).val()
          if(val.length >= 3) maps.routes.autocomplete.search(val,'to')
        })

        $(document).on('keyup','.addressSearch',function(e){
          const val = $(this).val()
          if(val.length >= 3) maps.routes.autocomplete.search(val,'address')
        })

        $(document).on("click",".autocompleteSelector",function(e){
          const acControl = $(this).closest(".autocompleteControl").attr("ac-control")
          const address = $(this).text()

          maps.routes.autocomplete.select(acControl,address)
        })

        $(document).on("click",".btTracarRota",function(e){
          maps.routes.fromAtoB()
        })
        $(document).on("click",".btClearRouting",function(e){
          maps.routes.clear()
        })
        
        $(document).on("click",".btWhereAmI",function(e){
          maps.whereAmI.getLocation()
        })

        $(document).on("click",".mapModeToggle",function(e){
          maps.nightMode.toggle()
        })
        $(document).on("click",".mapSearchToggle",function(e){
          maps.locationSearch.toggle()
        })
        
        $(document).on("click",".follow",function(e){
          let id = $(this).attr("unit-control")
          let description = $(this).attr("description")
          let pos = $(this).attr("pos-control")

          maps.follow.start(id, description, pos)
        })
        $(document).on("click",".stopFollow",function(e){
          maps.follow.end()
        })

        
        maps.options.eventsLoaded = true
      }
    },
    run: () => {
      maps.config(true)
      
      // maps.nightMode.on()
      // blustock.filter.open()
      // window.alert('teste')
    }
  }
  
  maps.run()

})
