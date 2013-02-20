<div class="form-item">
    <label>Parameters:</label><br class="cb" />
    <div class="description tsml">All user defined parameters go here 
        (parameters for your view/action helper.</div>
    <br />
    <div style="width: 550px; height: 240px; overflow: auto;
         border: 1px solid #CCCCCC; background: #BBBBBB;">
         <?php
         // Total number should be divided
         $paramPrefix = $this->form->getUserParamsPrefix();
         $paramLimit = $this->form->getUserParamsLimit();
         $fieldNamesPerParam = $this->form->getFieldNamesPerUserParam();

         // Used to alternate between ";" and "=>" for output
         $flag = 0;
         for ($i = 0; $i < $paramLimit; $i += 1) {
             foreach ($fieldNamesPerParam as
             $humanReadable => $formName) {
                 $index = $i + 1;
                 $paramName = $paramPrefix . $index . '_' . $formName;
                 echo $this->form->$paramName;
                 $flag = $flag ? 0 : 1;
                 echo ($flag) ? '<span class="form-item block">=></span>' :
                         '<br class="cb" />';
             }
         }
         ?><br class="cb" />
    </div>
</div> <!--/.form-item-->