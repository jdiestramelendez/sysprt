$(document).ready(function(){

  window.users = {
    options: {
      eventsLoaded: false,
      multipleGroups: [2,3]
    },
    dealerGroupSelect: {
      track: () => {
        const self = users.dealerGroupSelect

        if($(".roleBox").length > 0){
          const selected = $(".roleBox input[checked]").val()
          self.changeInput(selected)
        }
      },
      changeInput: (val) => {
        val = Number(val)
        const mg = users.options.multipleGroups
        const group = (mg.indexOf(val) !== -1 ? "dealer" : "all")
        console.log('val', val)
        setTimeout(() => {
          $(".dealerGroupSelect").addClass("off")
                                 .find("select").attr("disabled",true)
          $(".dealerGroupSelect[data-set='" + group + "']").removeClass("off")
                                                           .find("select").attr("disabled", false)
        },500)
        
      }
    },
    events: () => {
      if (!users.options.eventsLoaded){

        $(document).on("mousedown", ".roleBox", function(e){
          const value = $(this).find('input.iRadio').val()
          users.dealerGroupSelect.changeInput(value)
        })

        users.options.eventsLoaded = true
      }
      
    },
    run: () => {
      users.events()

      //on load get multiple select status
      users.dealerGroupSelect.track()
    }
  }

  users.run()
})