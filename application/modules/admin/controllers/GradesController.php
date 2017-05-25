<?php

class Admin_GradesController extends Zend_Controller_Action
{
  protected $_session;
  
  public function init()
    {
      /* Initialize action controller here */
      $this->_session = new Zend_Session_Namespace('Grades');
      $this->view->headTitle('View Grades');
      $this->view->pageHeader = 'View Grades';
    }

    public function getForm()
    {
            return new Form_ViewGrades(array(
                    'action' => 'view',
                    'method' => 'post'
                    ));
    }

    public function indexAction()
    {
      $this->_redirect('admin/grades/view');
    }

    public function viewAction()
    {
            $form = $this->getForm();
            $this->view->form = $form;

            if (!$this->getRequest()->isPost())
            {
                    return $this->render('view');
            }

            $formData = $this->getRequest()->getPost();

            if (!$form->isValid($formData))
            {
                    $form->populate($formData);
                    $this->view->form = $form;
                    return $this->render('view');
            }

            // get the selected week to view grades
            $week = $this->_session->week = $form->getValue('week');
            $this->view->week = $week;

            // create model object and access method to get grades
            $workoutData = new Model_WorkoutData();
            $grades = $this->_session->grades = $workoutData->getWeeklyGrade($week);

            // add the view object for the grades
            $this->view->grades = $grades;
            return $this->render('view');
    }

    public function csvAction()
    {
           // turn off the view and the layout
            $this->_helper->viewRenderer->setNoRender();
            $this->_helper->layout->disableLayout();
            
            $week = $this->_session->week;

            // create a filename, set the headers and open the output stream
            $filename = "week_" . $week . "_grades.csv";
            header('Content-Type: text/csv; charset=utf-8');
            header('Content-disposition: attachment; filename=' . $filename);
            $fp = fopen('php://output', 'w');

            // output the column headings
            $fields = array('id','grade','firstname','lastname');
            fputcsv($fp, $fields,',');
            foreach ($this->_session->grades as $row) {
              fputcsv($fp, $row);
            }
 
            // close the file pointer
            fclose($fp);
    }

}

