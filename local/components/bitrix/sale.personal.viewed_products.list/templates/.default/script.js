$(function() {
    
   $(document).on('click', '.js-delete-viewed-product', function() {
      var self = $(this);
      var product_id = self.closest('tr').data('product_id');
      BX.showWait();
      $.post(
        '',
        {
            product_id: product_id,
            action: 'delete'
        },
        function(data) {
            $('#viewed_products').html($(data).find('#viewed_products').html());
            BX.closeWait();
        }
      );
   });
   
});