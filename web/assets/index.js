var Kicker = function () {
    this.app = $('#application');
    this.playersForm = $('#players-form');
    this.generateScheduleButton = $('#generate-schedule').hide();
    this.matchesTable = this.app.find('.match-table');
    this.players = [];
    this.bind();
};

Kicker.prototype = {
    bind: function () {
        this.app.on('click', '#generate-schedule', this.generate.bind(this));
        this.app.on('click', '#add-player', this.onAddPlayer.bind(this));
        this.app.on('click', '.remove-player', this.onRemovePlayer.bind(this));
        this.app.on('click', '#add-player, .remove-player', this.validateGenerationButton.bind(this));
    },

    generate: function () {
        this.players = _.reduce(this.playersForm.find('.form-line'), function(mem, line){
            mem.push($(line).find('input').val());
            return mem;
        }, []);
        var teams = [];

        for (var i = 0; i < this.players.length; i++) {
            for (var j = i + 1 ; j < this.players.length; j++) {
                teams.push([this.players[i], this.players[j]]);
            }
        }

        var matches = [];
        for (var i = 0; i < teams.length; i++) {
            for (var j = i + 1 ; j < teams.length; j++) {
                if (teams[i][0] !== teams[j][0] && teams[i][0] !== teams[j][1] && teams[i][1] !== teams[j][0] && teams[i][1] !== teams[j][1]) {
                    matches.push([teams[i], teams[j]]);
                }
            }
        }

        this.renderMatchesTable(matches);
    },

    renderMatchesTable: function (matches) {
        _.each(matches, function (match) {
            console.log(match);
        });
    },

    onAddPlayer: function(e) {
        this.playersForm.append($('#player-template').html());
    },

    onRemovePlayer: function(e) {
        $(e.currentTarget).closest('.form-line').remove();
    },

    validateGenerationButton: function () {
        this.generateScheduleButton.hide();

        if (this.playersForm.find('.form-line').length >= 4) {
            this.generateScheduleButton.show();
        }
    }
};