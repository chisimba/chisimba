<?php
    $this->loadClass('form','htmlelements');
    $this->loadClass('textarea','htmlelements');
    $this->loadClass('button','htmlelements');
    $this->loadClass('htmltable','htmlelements');
    //$form = & $this->newObject('form','htmlelements');
    
    $table = new htmltable();
    $table->cellpadding = '5';
    $table->cellspacing = '2';
    
    //setup form
    $form = new form('frm_dublincore', $this->uri(array('action'=>'save')));
    
    //title
    $title = new textarea('title', '', 2, 50);
    $label = $this->objLanguage->languageText("word_title");
    $table->addRow(array($label.': ', $title->show()));
    
    //subject
    $subject = new textarea('subject', '', 2, 50);
    $subject->name = 'subject';
    $label = $this->objLanguage->languageText("mod_dublin_subject", 'dublincoremetadata');
    $table->addRow(array($label.': ', $subject->show()));
    
    //description
    $description = new textarea('description', '', 2, 50);
    $description->name = 'description';
    $label = $this->objLanguage->languageText("mod_dublin_description", 'dublincoremetadata');
    $table->addRow(array($label.': ', $description->show()));
    
    //source
    $source = new textarea('source', '', 2, 50);
    $source->name = 'source';
    $label = $this->objLanguage->languageText("mod_dublin_source", 'dublincoremetadata');
    $table->addRow(array($label.': ', $source->show()));
    
    //type
    $type = new textarea('type', '', 2, 50);
    $type->name = 'type';
    $label = $this->objLanguage->languageText("mod_dublin_type", 'dublincoremetadata');
    $table->addRow(array($label.': ', $type->show()));
    
    //coverage
    $coverage = new textarea('coverage', '', 2, 50);
    $coverage->name = 'coverage';
    $label = $this->objLanguage->languageText("mod_dublin_coverage", 'dublincoremetadata');
    $table->addRow(array($label.': ', $coverage->show()));
    
    //creator
    $creator = new textarea('creator', '', 2, 50);
    $creator->name = 'creator';
    $label = $this->objLanguage->languageText("mod_dublin_creator", 'dublincoremetadata');
    $table->addRow(array($label.': ', $creator->show()));
    
    //publisher
    $publisher = new textarea('publisher', '', 2, 50);
    $publisher->name = 'publisher';
    $label = $this->objLanguage->languageText("mod_dublin_publisher", 'dublincoremetadata');
    $table->addRow(array($label.': ', $publisher->show()));
    
    //contributor
    $contributor = new textarea('contributor', '', 2, 50);
    $contributor->name = 'contributor';
    $label = $this->objLanguage->languageText("mod_dublin_contributor", 'dublincoremetadata');
    $table->addRow(array($label.': ', $contributor->show()));
    
    //rights
    $rights = new textarea('rights', '', 2, 50);
    $rights->name = 'rights';
    $label = $this->objLanguage->languageText("mod_dublin_rights", 'dublincoremetadata');
    $table->addRow(array($label.': ', $rights->show()));

    //relationship
    $date = new textarea('date', '', 2, 50);
    $date->name = 'date';
    $label = $this->objLanguage->languageText("mod_dublin_date", 'dublincoremetadata');
    $table->addRow(array($label.': ', $date->show()));
    
    //format
    $format = new textarea('format', '', 2, 50);
    $format->name = 'format';
    $label = $this->objLanguage->languageText("mod_dublin_format", 'dublincoremetadata');
    $table->addRow(array($label.': ', $format->show()));
    
    //relationship
    $relationship = new textarea('relationship', '', 2, 50);
    $relationship->name = 'relationship';
    $label = $this->objLanguage->languageText("mod_dublin_relationship", 'dublincoremetadata');
    $table->addRow(array($label.': ', $relationship->show()));
    
    //identifier
    $identifier = new textarea('identifier', '', 2, 50);
    $identifier->name = 'identifier';
    $label = $this->objLanguage->languageText("mod_dublin_identifier", 'dublincoremetadata');
    $table->addRow(array($label.': ', $identifier->show()));
    
    //language
    $language = new textarea('relationship', '', 2, 50);
    $language->name = 'relationship';
    $label = $this->objLanguage->languageText("mod_dublin_language", 'dublincoremetadata');
    $table->addRow(array($label.': ', $language->show()));
    
    //audience
    $audience = new textarea('audience', '', 2, 50);
    $audience->name = 'audience';
    $label = $this->objLanguage->languageText("mod_dublin_audience", 'dublincoremetadata');
    $table->addRow(array($label.': ', $audience->show()));
    
    $form->addToForm($table->show());
    
    $objButton = new button('save');
    $objButton->setToSubmit();
    $objButton->setValue($this->objLanguage->languageText("mod_contextadmin_save", 'contextadmin'));
    $form->addToForm($objButton->show());
    
    echo '<h1>'. $this->objLanguage->languageText("mod_dublin_dcm", 'dublincoremetadata'). '</h1>';
    echo $form->show();
    
?>