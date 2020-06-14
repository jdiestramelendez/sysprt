$(document).ready(function () {

  window.configs = {
    options: {
      eventsLoaded: false,
      allowSubmit: false
    },
    start: () => {

    },
    getUnitID: (serial) => {
      const options = {
        "serial_unit": serial
      }
      axios.post('/getunitid',options)
      .then(function (response) {
        // handle success
        console.log("response: ", response);
        if(response.data.msg === "Unidade encontrada"){
          $('#id_unit').val(response.data.UnitId).change()
        }else{
          $('#id_unit').val('').change()
        }
      })
      .catch(function (error) {
        // handle error
        console.log("error: ", error);
      })
      .then(function () {
        // always executed
        // self.progress.remove()
      });
    },
    saveAsset: () => {
      // const options = {
      //   "serial_unit": serial
      // }
      console.log("saveAsset..")

      var data = $('.assetForm').serializeArray()
      
      var drop = {}
      data.map(item => {
        var key = item.name
        var value = item.value
        
        drop[key] = value
      })
      
      console.log("data", data)
      console.log("drop", drop)
      blustock.template.spinner.on()
      var id = $("#asset_id").val()

      if(id === "") {
        //CREATE
        axios.post('/assets', drop)
        .then(function (response) {
          // console.log("saveAsset", response.data)
          if(response.data.msg !== "Algo deu errado."){
            $(".response").text(response.data.msg)
            $(".alertModal").addClass("on")
          }
        })
        .catch(function (error) {
          // handle error
          console.log("error: ", error)
        })
        .then(function () {
          blustock.template.spinner.off()
          // always executed
          // self.progress.remove()
        });
        
      } else {
        //EDIT
        axios.put('/assets/' + id, drop)
        .then(function (response) {
          // console.log("saveAsset", response.data)
          if(response.data.msg !== "Algo deu errado."){
            $(".response").text(response.data.msg)
            $(".alertModal").addClass("on")
          }
        })
        .catch(function (error) {
          // handle error
          console.log("error: ", error)
        })
        .then(function () {
          blustock.template.spinner.off()
          // always executed
          // self.progress.remove()
        });
      }      
      
    },
    events: () => {
      if(!configs.options.eventsLoaded){
        // $(document).on()
        $(document).on('click','.submitCreateAsset', function (e){
          e.preventDefault()
          configs.saveAsset()
          // if(val.length === 0) 
        })
        
        configs.options.eventsLoaded = true
      }
    },
    run: () => {
      configs.events()

      // if($(".getUnitID").length > 0){
      //   let iniValue = $(".getUnitID").val()
      //   // console.log("iniValue: ",iniValue)
      //   if(iniValue.length > 0){
      //     configs.getUnitID(iniValue)
      //   }
      // }
    }
  }

  configs.run()
})