var Tournament = function () {
    this.page = $('#page');
    this.playersForm = $('#players-form');
    this.generateScheduleButton = $('#generate-schedule').hide();
    this.matchesTable = this.page.find('.tournament-table');
    this.matchForm = new MatchForm(this.matchesTable, true);
    this.bind();
};

Tournament.prototype = {
    bind: function () {
        this.page.on('click', '#generate-schedule', this.generate.bind(this));
        this.page.on('click', '#add-player', this.onAddPlayer.bind(this));
        this.page.on('click', '.remove-player', this.onRemovePlayer.bind(this));
        this.page.on('click', '#add-player, .remove-player', this.validateGenerationButton.bind(this));
    },

    generate: function () {
        var players = _.reduce(this.playersForm.find('.form-line'), function(mem, line){
            mem.push($(line).find('select').val());
            return mem;
        }, []);
        var teams = [];

        for (var i = 0; i < players.length; i++) {
            for (var j = i + 1 ; j < players.length; j++) {
                teams.push({goalkeeper: players[i], forward: players[j]});
            }
        }

        var matches = [];
        for (var i = 0; i < teams.length; i++) {
            for (var j = i + 1 ; j < teams.length; j++) {
                if (teams[i].goalkeeper !== teams[j].goalkeeper && teams[i].goalkeeper !== teams[j].forward && teams[i].forward !== teams[j].goalkeeper && teams[i].forward !== teams[j].forward) {
                    matches.push({redTeam: teams[i], blueTeam: teams[j]});
                    matches.push({redTeam: teams[j], blueTeam: teams[i]});
                }
            }
        }

        this.renderMatchesTable(matches);
    },

    renderMatchesTable: function (matches) {
        this.matchesTable.html('');

        _.each(matches, function (match) {
            this.matchForm.render(match);
        }, this);
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