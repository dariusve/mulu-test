<div class="wrapper wrapper-content animated fadeInUp">
<div class="page-header">
    <h1>Contacts Grouping</h1>
    <p id="msg" class="lead"></p>
</div>
<div id="agents-form" class="panel panel-default" style="display:none;">
    <div class="panel-heading">
        <h3 class="panel-title">Agents Zipcodes</h3>
    </div>
    <div class="panel-body">
        <div id="input-form">
            <div class="row">
                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-addon" id="basic-addon1">Agent 1</span>
                        <input id='zip1' type="text" class="form-control" placeholder="Type the agent zip code here" aria-describedby="basic-addon1" data-agent='1' autocomplete="false" value="">
                    </div>
                    <span class="help-block m-b-none"><small id='msg1'></small></span>
                </div>
            <div class="col-md-6">
                <div class="input-group">
                    <span class="input-group-addon" id="basic-addon1">Agent 2</span>
                    <input  id='zip2' type="text" class="form-control" placeholder="Type the agent zip code here" aria-describedby="basic-addon2"  data-agent='2' autocomplete="false">
                </div>
                <span class="help-block m-b-none"><small id='msg2'></small></span>
            </div>
        </div>
        <div class="hr-line-dashed"></div>
        <div class='row'>
            <div class="col-md-12">
                <div class="">
                    <button id='group' class="btn btn-primary pull-right">Group it!</button>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
<div id="tresults" class="panel panel-default" style="display:none;">
    <div class="panel-heading">Results</div>
    <table id='results' class="table-condensed table-hover table-stripped">
        <thead>
            <tr>
                <th class="text-center">Agent ID</th>
                <th class="text-center">Contact Name</th>
                <th class="text-center">Contact Zipcode</th>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
</div>
</div>
<script>
    var geocoder;
    var ncontacts = [];
    var count_p = 0;
    var errors = [];
    var agents = [];
    var agentst = [];
    var clicked = false;
    // contact list received at page load
    var contacts = <?php echo json_encode($contacts); ?>;

    // number of contacts
    var cc = contacts.length;
    
    (function($){
        errors['agent1'] = false;
        errors['agent2'] = false;

        // At load display a message while getting th contacts location
        $('#msg').html('Please wait Getting Contacts Location <i class="fa fa-refresh fa-spin"></i>');

        // get the geographical location for every contact
        $.each(contacts, function(i,c){
            // defered Ajax call to the Geonames API
            var aj1 =$.ajax({
                url:"http://api.geonames.org/postalCodeLookupJSON?postalcode="+c.zipcode+"&country=US&username=edus",
            });
            aj1.done(function(r){
                // response from the API
                var result = r;
                if (result.postalcodes.length > 0){
                    // it's a valid response
                    info = result.postalcodes[0];

                    // build new contacts array including the latitude an longitudes obtained via API
                    nc = {
                        name: c.name,
                        zipcode: c.zipcode,
                        location: {
                            lat:info.lat,
                            lng:info.lng
                        }
                    }
                    // store the contact
                    ncontacts.push(nc);
                } else {
                    // not a valid zipcode
                    console.log('zipcode invalid: '+c.zipcode);
                }
                count_p ++;

                // as soon all the contacts are processed, show the Agents form
                if (count_p === cc){
                    $('#msg').text('Done!');
                    $('#agents-form').show();
                }
            });
        });
        
        // send the contacts list and the agents list via an Ajax call to the group method on the controller
        // 
        function groupContacts(a,c){
            $.ajax({
                url: "<?php echo $this->Html->url(array('controller'=>'agents','action'=>'group')); ?>",
                type: 'POST',
                dataType:"json",
                data: {
                    a: a,
                    c: c
                },
                success: function(r){
                    //show the result
                    $.each(r.agent1,function(i,d){
                        st = "<tr><td>1</td><td>"+d.name+"</td><td>"+d.zipcode+"</td></tr>";
                        $('#results > tbody').append(st);
                    });
                    $.each(r.agent2,function(i,d){
                        st = "<tr><td>2</td><td>"+d.name+"</td><td>"+d.zipcode+"</td></tr>";
                        $('#results > tbody').append(st);
                    });
                    $('#tresults').show();
                }
            });
        }

        // Events Capture

        // Verifies that's a zipcode has been entered
        $('input[type=text]').on('blur',function(){
            zipcode = $(this).val();
            th = this;
            if (zipcode.length > 0){
                $(th).parent().addClass('has-error');
                $(th).removeClass('loadinggif');
                $('#msg'+agent).text('You must provide a valid zip code');
            }
        });

        // clear the error messages 
        $('input[type=text]').on('click',function(){
            agent = $(this).context.dataset.agent;
            $(this).parent().removeClass('has-error');
            $(this).parent().removeClass('has-success');
            $(this).removeClass('loadinggif');
            $('#msg'+agent).text('');
        });

        // when the user clicks the Group it! button, prepares to send the request to the Agents Cotroller
        $('#group').on('click',function(){
            $('#group').html('<i class="fa fa-refresh fa-spin fa-fw"></i> Group');
            //$('#group').attr('disable','disabled');
            clicked = true;
            agents = [];
            agentst = [];
            zip1 = $('#zip1').val();
            zip2 = $('#zip2').val();
            
            if (zip1.length <= 0) {
                $("#zip1").parent().addClass('has-error');
                $("#zip1").removeClass('loadinggif');
                $('#msg1').text('Invalid Zipcode');
                errors['agent1'] = true;
                $('#group').html('Group');
                $('#group').attr('disable','enabled');
            } else {
                errors['agent1'] = false;
            }
            if (zip2.length <= 0) {
                $("#zip2").parent().addClass('has-error');
                $("#zip2").removeClass('loadinggif');
                $('#msg2').text('Invalid Zipcode');
                errors['agent2'] = true;
                $('#group').html('Group');
                $('#group').attr('disable','enabled');
            } else {
                errors['agent2'] = false;
            }
            if (zip1.length > 0 && zip2.length > 0) {
                agent1 = {
                    id : 1,
                    name:'Agent 1',
                    zipcode: zip1
                }
                agents.push(agent1);
                agent2 = {
                    id: 2,
                    name:'Agent 1',
                    zipcode: zip2
                }
                agents.push(agent2);
                na = agents.length;
                ca = 0;
                $('#results > tbody').html('');
                $.each(agents, function(i,a){
                    var q =$.ajax({
                        url:"http://api.geonames.org/postalCodeLookupJSON?postalcode="+a.zipcode+"&country=US&username=edus"
                    });
                    q.done(function(r){
                        var result = r;
                        if (result.postalcodes.length > 0){
                            info = result.postalcodes[0];
                            nc = {
                                name: a.name,
                                zipcode: a.zipcode,                    
                                location: {
                                    lat:info.lat,
                                    lng:info.lng
                                }
                            }
                            agentst.push(nc);
                            console.log(agentst)
                            errors['agent'+a.id] = false;
                        } else {
                            console.log(a.name+' Zipcode Invalid');
                            console.log(' Zipcode Invalid');
                            console.log(zipcode);
                            $("#zip"+a.id).parent().addClass('has-error');
                            $("#zip"+a.id).removeClass('loadinggif');
                            $('#msg'+agent).text('Invalid Zipcode');
                            errors['agent'+a.id] = true;
                            console.log(errors);
                        }
                        $('#group').html('Group');
                        $('#group').attr('disable','enabled');
                        ca ++;
                        if (ca == na){
                            console.log('ok')
                            console.log(errors);
                            if (!errors['agent1'] && !errors['agent2']){
                                console.log(agentst)
                                groupContacts(agentst,ncontacts);
                                $('#group').html('Group it!');
                            } 
                        }
                    });
                    
                });
            }
        });

    })(jQuery)
</script>
