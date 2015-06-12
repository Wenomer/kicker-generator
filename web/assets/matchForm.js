var MatchForm = function (target, isTournament) {
    this.isTournament = isTournament;
    this.target = target;
    this.template = _.template($('#match-form-template').html());
    this.bind();
};

MatchForm.prototype = {
    render: function(match) {
        match = match || {redTeam: {goalkeeper: 0, forward: 0}, blueTeam: {goalkeeper: 0, forward: 0}};
        this.target.append(this.template({match: match, isTournament: this.isTournament}));
    },

    bind: function() {
        var self = this;
        $('#page').on('submit', 'form', function(e){
            var form = $(e.currentTarget);

            $(this).ajaxSubmit({
                dataType: 'json',
                success: function(response) {
                    if (response && response.success) {
                        if (!self.isTournament) {
                            self.clearForm(form);
                        } else {
                            self.blockForm(form);
                        }
                    }
                }
            });
            return false;
        });
    },

    blockForm: function(form) {
        form.css('background-color', '#5cb85c');
        form.find('input[type="text"], input[type="submit"]').prop('disabled', true);
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