<br>
<br>
<br>
<br>
<section class="section-1">
    <div class="container">
        <div class="row">
            <div class="text-center mrg-btm-50">
                <h2 class="heading-1 text-center">API credentials missing</h2>
                <p class="width-40 mrg-horizon-auto">Please, enter the API token and URL below</p>
            </div>

            <div class="col-md-6 col-md-offset-3 contact-form">
                <form class="contact-form-wrapper margin-10"  name="contactForm" id='contact_form' method="post">
                    <div class="row">
                        <div class="form-group col-md-12">
                            <input type="text" class="form-control" name="api_url" placeholder="URL" value="<?php echo $api_url?>">
                            <span class="small-alert">
                                    <?php
                                    echo $this->showErrors(API_URL);
                                    ?>
                                </span>
                        </div>
                        <div class="form-group col-md-12">
                            <input type="text" class="form-control" name="api_token" placeholder="Access token"  value="<?php echo $api_token?>">
                            <span class="small-alert">
                                    <?php
                                    echo $this->showErrors(API_TOKEN);
                                    ?>
                                </span>
                        </div>
                        <div class="form-group col-md-12" id="submit">
                            <input class="btn btn-md btn-dark" type="submit" id="send_message" value="Save">
                        </div>
                    </div>
                </form>
            </div>
        </div>

    </div>
</section>