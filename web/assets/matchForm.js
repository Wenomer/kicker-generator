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
            e.preventDefault();
            e.stopPropagation();

            var form = $(e.currentTarget);

            self.round++;

            if(self.validate(form)) {
                form.css('background-color', 'white');
                form.find('input[type=submit]').attr('disabled', 'disabled');

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
                        form.find('input[type=submit]').removeAttr('disabled');
                        //self.excludePlayers(form);
                        self.getProbability(form);
                        self.updateAvatars(form);
                    }
                });
            } else {
                form.css('background-color', '#d9534f');
            }

            return false;
        });

        this.target.on('change', 'select', function(e) {
            var select = $(e.currentTarget);
            var form = select.closest('form');
            select.closest('.media').find('img').attr('src', 'https://www.gravatar.com/avatar/' + select.find('option:selected').data('hash') + '?s=50');
            //self.excludePlayers(form);
            self.getProbability(form);
            self.updateAvatars(form);
        });
    },

    validate: function (form) {
        var result = true;
        var selects = form.find('select');
        var inputsRed = form.find('input[name="match[red_score]"]');
        var inputsBlue = form.find('input[name="match[blue_score]"]');
        var selected = [];

        _.each(selects, function(select) {
            select = $(select);
            if (select.val() == 0 || $.inArray(select.val(), selected) != -1) {
                result = false;
            }
            selected.push(select.val());
        });

        if (inputsRed.val() == "" || inputsBlue.val() == "" || (inputsRed.val() != 5 && inputsBlue.val() != 5)) {
            result = false;
        }

        return result;
    },

    updateAvatars: function (form) {
        var selects = form.find('select');

        _.each(selects, function(select) {
            select = $(select);
            select.closest('.media').find('img').attr('src', 'https://www.gravatar.com/avatar/' + select.find('option:selected').data('hash') + '?s=50');
        });
    },

    getProbability: function (form) {
        var selects = form.find('select');
        var probability = form.find('.probability');

        var values = _.reduce(selects, function (mem, select){
            select = $(select);
            if (select.val() != 0) {
                mem[select.attr('name').replace("match[", "").replace("]", "")] = select.val();
            }

            return mem;
        }, {});

        if (_.size(values) === 4) {
            $.getJSON('/api/probability', {match: values}, function (response) {
                if (response.data.redWin > response.data.blueWin) {
                    probability.html('   (' + response.data.redWin + '% Red Win)');
                } else {
                    probability.html('   (' + response.data.blueWin + '% Blue Win)');
                }
            });
        } else {
            probability.html('');
        }
    },

    excludePlayers: function (form) {
        var selects = form.find('select.form-control');
        var options = $('#player-options');

        var values = _.reduce(selects, function (mem, select){
            select = $(select);
            var val = select.val();

            if (val != 0) {
                mem.push(val);
            }
            select.html(options.html());

            if (select.hasClass('goalkeeper')) {
                select.find('.forward').remove();
            } else {
                select.find('.goalkeeper').remove();
            }
            select.val(val);

            return mem;
        }, []);

        _.each(selects, function(select) {
            select = $(select);

            _.each(values, function(value) {
                if (select.val() != value) {
                    select.find('option[value="' + value + '"]').remove();
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