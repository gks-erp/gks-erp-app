

<div id="dialog_payment_with" title="<?php echo $GKS_SITE_HUMAN_NAME;?>" style="display: none;">
  <div id="dialog_payment_with_div1">
    <div id="dialog_payment_with_row1">  
      <div id="dialog_payment_with_title"><?php echo gks_lang('Πληρωμή με POS');?></div>
    </div>
    <div>  
      <table style="width:100%" cellpadding="10">
        <tbody><tr>
          <td id="dialog_payment_with_td1" rowspan="10">
            <i id="dialog_payment_with_icon" class="far fa-credit-card" style=""></i>
            <i id="dialog_payment_with_icon2" class="" style=""></i>
          </td>
          <td id="dialog_payment_with_td2">
            <div id="dialog_payment_with_text1"></div>

            <div id="dialog_payment_with_tip">
              <div class="dialog_payment_with_tip1">
                <div class="dialog_payment_with_tip1a">
                  <?php echo gks_lang('Φιλοδώρημα');?>:
                </div>
                <div class="dialog_payment_with_tip1b">
                  <input id="dialog_payment_with_tip_val"class="form-control form-control-sm" type="number" value="0" autocomplete="off" min="0">
                </div>
                <div class="dialog_payment_with_tip1c">
                  <select class="form-control form-control-sm" id="dialog_payment_with_tip_pososto">
                    <option value="0">0</option>
                    <option value="1">1%</option>
                    <option value="2">2%</option>
                    <option value="3">3%</option>
                    <option value="4">4%</option>
                    <option value="5">5%</option>
                    <option value="6">6%</option>
                    <option value="7">7%</option>
                    <option value="8">8%</option>
                    <option value="9">9%</option>
                    <option value="10">10%</option>
                  </select>
                </div>
              </div>
            </div>
            <div id="dialog_payment_with_doseis">
              <div class="dialog_payment_with_doseis1">
                <div class="dialog_payment_with_doseis1a">
                  <?php echo gks_lang('Δόσεις');?>:
                </div>
                <div class="dialog_payment_with_doseis1b">
                  <select class="form-control form-control-sm" id="dialog_payment_with_doseis_val">
                    <option value="0"><?php echo gks_lang('Χωρίς δόσεις');?></option>
                    
                    <option value="2">2</option>
                    <option value="3">3</option>
                    <option value="4">4</option>
                    <option value="5">5</option>
                    <option value="6">6</option>
                    <option value="7">7</option>
                    <option value="8">8</option>
                    <option value="9">9</option>
                    <option value="10">10</option>
                    <option value="11">11</option>
                    <option value="12">12</option>
                    <option value="13">13</option>
                    <option value="14">14</option>
                    <option value="15">15</option>
                    <option value="16">16</option>
                    <option value="17">17</option>
                    <option value="18">18</option>
                    <option value="19">19</option>
                    <option value="20">20</option>
                    <option value="21">21</option>
                    <option value="22">22</option>
                    <option value="23">23</option>
                    <option value="24">24</option>
                    <option value="25">25</option>
                    <option value="26">26</option>
                    <option value="27">27</option>
                    <option value="28">28</option>
                    <option value="29">29</option>
                    <option value="30">30</option>
                    <option value="31">31</option>
                    <option value="32">32</option>
                    <option value="33">33</option>
                    <option value="34">34</option>
                    <option value="35">35</option>
                    <option value="36">36</option>
                    <option value="37">37</option>
                    <option value="38">38</option>
                    <option value="39">39</option>
                    <option value="40">40</option>
                    <option value="41">41</option>
                    <option value="42">42</option>
                    <option value="43">43</option>
                    <option value="44">44</option>
                    <option value="45">45</option>
                    <option value="46">46</option>
                    <option value="47">47</option>
                    <option value="48">48</option>
                    <option value="49">49</option>
                    <option value="50">50</option>
                    <option value="51">51</option>
                    <option value="52">52</option>
                    <option value="53">53</option>
                    <option value="54">54</option>
                    <option value="55">55</option>
                    <option value="56">56</option>
                    <option value="57">57</option>
                    <option value="58">58</option>
                    <option value="59">59</option>
                    <option value="60">60</option>
                  </select>
                </div>
              </div>
            </div> 
            <div id="dialog_payment_with_preferred_payment_method">
              <div id="dialog_payment_with_ppm_text">
                <?php echo gks_lang('Τρόπος');?>:
              </div>
              <div id="dialog_payment_with_ppm_tap" class="tooltipster" title="Tap to Pay<br>Credit Card<br>Debit Card">
                <input type="radio" name="dialog_payment_with_ppm_radio" value="tap" id="dialog_payment_with_ppm_radio_tap">
                <label for="dialog_payment_with_ppm_radio_tap"><img src="img/tap_to_pay.svg"></label>
              </div>
              <div id="dialog_payment_with_ppm_iris" class="tooltipster" title="IRIS Payments">
                <input type="radio" name="dialog_payment_with_ppm_radio" value="iris" id="dialog_payment_with_ppm_radio_iris">
                <label for="dialog_payment_with_ppm_radio_iris"><img src="img/payment_iris_200.png"></label> 
              </div>
            </div>
            
            <div id="dialog_payment_with_refund">
              <div class="dialog_payment_with_refund1">
                <div class="dialog_payment_with_refund1a">
                  <?php echo gks_lang('Επιστροφή');?>:
                </div>
                <div class="dialog_payment_with_refund1b">
                  <input id="dialog_payment_with_refund_val"class="form-control form-control-sm" type="number" value="0" autocomplete="off" min="0">
                </div>
              </div>
            </div>                       
            <div id="dialog_payment_with_text2"></div>
            <div id="dialog_payment_with_delete">
              <button class="btn-sm btn-danger" id="dialog_payment_with_delete_run"><?php echo gks_lang('Ακύρωση');?></button>  
              <a id="dialog_payment_show_card_terminal_btn_viva" href="vivapayclient://pay/v1?merchantKey=0000000000&appId=com.gks_gr.gks_erp_app_mobile&action=foreground&callback=" target="_blank" class="btn btn-sm btn-info" style="display:none;"><?php echo gks_lang('Εμφάνιση POS');?></a>
            </div>
            <div id="dialog_payment_with_text3"></div>
            <div id="dialog_payment_with_text4"></div>
            <div id="dialog_payment_with_text5"></div>
            <div id="dialog_payment_with_text_error"></div>
            <div id="dialog_payment_show_card_terminal">
            </div>
          </td>
        </tr><tr>
          <td id="dialog_payment_with_tdpb">
            <img id="dialog_payment_with_pb" src="/my/img/progress_bar2.gif">
          </td>          
        </tr></tbody>
      </table>
          
    </div>
    
  </div>
</div>