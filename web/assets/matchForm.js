var MatchForm = function (target) {
    this.target = target;
    this.template = _.template($('#match-form-template').html());
    this.bind();
};

MatchForm.prototype = {
    render: function(data) {
        this.target.append(this.template(data));
    },

    bind: function() {
        var self = this;
        $('#page').on('submit', 'form', function(e){
            var form = $(e.currentTarget);

            $(this).ajaxSubmit({
                dataType: 'json',
                success: function(response) {
                    if (response && response.success) {
                        self.clearForm(form);
                    }
                }
            });
            return false;
        });
    },

    clearForm: function(form) {
        form.find('select [value=0]').prop('selected', true);
        form.find('input[type="text"]').val('');
        form.css('background-color', '#5cb85c');

        setInterval(function() {
            form.css('background-color', 'white');
        }, 1500)
    }
};