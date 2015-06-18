var MatchForm = function (target, isTournament) {
    this.isTournament = isTournament;
    this.target = target;
    this.template = _.template($('#match-form-template').html());
    this.bind();
    this.round = 0;
};

MatchForm.prototype = {
    render: function(match) {
        match = match || {redTeam: {goalkeeper: 0, forward: 0}, blueTeam: {goalkeeper: 0, forward: 0}};
        this.target.append(this.template({match: match, isTournament: this.isTournament}));
    },

    bind: function() {
        var self = this;
        this.target.on('submit', 'form', function(e){
            var form = $(e.currentTarget);

            self.round++;

            $(this).ajaxSubmit({
                dataType: 'json',
                success: function(response) {
                    if (response && response.success) {
                        if (!self.isTournament) {
                            if (self.round == 1) {
                                self.reverseForm(form);
                            } else {
                                self.round = 0;
                                self.clearForm(form);
                            }
                        } else {
                            self.blockForm(form);
                        }
                    }

                    self.excludePlayers(form);
                }
            });

            return false;
        });

        this.target.on('change', 'select', function(e) {
            var select = $(e.currentTarget);
            select.closest('.media').find('img').attr('src', 'https://www.gravatar.com/avatar/' + select.find('option:selected').data('hash') + '?s=50');
            self.excludePlayers(select.closest('form'));
        });
    },

    excludePlayers: function (form) {
        var selects = form.find('select');

        var values = _.reduce(selects, function (mem, select){
            $(select).find('option').show();
            var val = $(select).val();
            if (val != 0) {
                mem.push(val);
            }

            return mem;
        }, []);

        _.each(selects, function(select) {
            var $select = $(select);

            _.each(values, function(value) {
                if ($select.val() != value) {
                    $select.find('option[value="' + value + '"]').hide();
                }
            });
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
    },

    reverseForm: function(form) {
        var tempGoalkeeper = form.find('select[name="match[red_goalkeeper_id]"]').val();
        form.find('select[name="match[red_goalkeeper_id]"]').val(form.find('select[name="match[red_forward_id]"]').val());
        form.find('select[name="match[red_forward_id]"]').val(tempGoalkeeper);

        tempGoalkeeper = form.find('select[name="match[blue_goalkeeper_id]"]').val();
        form.find('select[name="match[blue_goalkeeper_id]"]').val(form.find('select[name="match[blue_forward_id]"]').val());
        form.find('select[name="match[blue_forward_id]"]').val(tempGoalkeeper);

        form.find('input[type="text"]').val('');
        form.css('background-color', '#5cb85c');

        setInterval(function() {
            form.css('background-color', 'white');
        }, 1500)
    }
};