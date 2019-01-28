<?php
// src/Command/InsertDocumentCommand.php
namespace App\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;
use App\Entity\Document;
use App\Entity\DocumentFile;
use App\Application\Sonata\MediaBundle\Entity\Media as Media;

class InsertDocumentCommand extends ContainerAwareCommand
{
	
	protected function configure()
    {
         $this
			// the name of the command (the part after "bin/console")
			->setName('app:insert-document')

			// the short description shown while running "php bin/console list"
			->setDescription('Création de documents')

			// the full command description shown when running the command with
			// the "--help" option
			->setHelp('Cette commande vous permet de créer des documents en masse via excel...')
		;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
		// Showing when the script is launched
        $now = new \DateTime();
        $output->writeln('<comment>Start : ' . $now->format('d-m-Y G:i:s') . ' ---</comment>');
		
        
        // Importing CSV on DB via Doctrine ORM
        $this->import($input, $output);
        
        // Showing when the script is over
        $now = new \DateTime();
        $output->writeln('<comment>End : ' . $now->format('d-m-Y G:i:s') . ' ---</comment>');
    }
	
	protected function get(InputInterface $input, OutputInterface $output) 
    {
        // Getting the CSV from filesystem
        $fileName = 'public/uploads/import/trigano_marque_document.csv';
        
        // Using service for converting CSV to PHP Array
        $converter = $this->getContainer()->get('import.csvtoarray');
        $data = $converter->convert($fileName, ';');
        
        return $data;
    }
	
	protected function import(InputInterface $input, OutputInterface $output)
    {
		// Getting php array of data from CSV
        $data = $this->get($input, $output);		
		
        // Getting doctrine manager
        $em = $this->getContainer()->get('doctrine')->getManager(); 
        // Turning off doctrine default logs queries for saving memory
        $em->getConnection()->getConfiguration()->setSQLLogger(null);
        
        // Define the size of record, the frequency for persisting the data and the current index of records
        $size = count($data);
        $batchSize = 20;//20
        $i = 1;
        
        // Starting progress
        $progress = new ProgressBar($output, $size);
        $progress->start();
		$output->writeln(' ');	
		
		$nb_error = 0;
		$tab_error = array();
		
		$ma_date =  new \DateTime();//date("Y-m-d H:i:s");
        
        // Processing on each row of data
        foreach($data as $row) {
			$output->writeln('<error>' . $row['titre'] . '</error>');
						
			$document 	= $em->getRepository('App:Document')->findOneBy(array('titre' => $row['titre']));
			$langue 	= $em->getRepository('App:Langue')->findOneBy(array('id' => $row['langue']));
			$type 		= $em->getRepository('App:DocumentType')->findOneBy(array('code' => $row['type'], 'langue' => $langue));

			if(!is_object($document)){
				$exist_deja = false;
				$document = new Document();
				//$output->writeln('<comment>nouveau document</comment>');
				//$document->setCreatedAt($ma_date);
			}else{
				//$output->writeln('<comment>document existe deja</comment>');
				//$document->setUpdatedAt($ma_date);
			}
			
			$document->setTitre($row['titre']);
			$document->setDescription($row['presentation']);
			$document->setDate(new \DateTime(str_replace('/','-', $row['dt'])));//excel->dd/mm/yyyy 		bdd->yyyy-mm-dd
			$document->setLangue($langue);
			$document->setRawContent($row['presentation']);
			$document->setContentFormatter('markdown');
			$document->setExercice($row['exercice']);
			$document->setDocumentType($type);
			//$document->setInSlide($row['news']);
			if($row['calendrier']=='-1'){
				$document->setInCalendar(true);
			}else{
				$document->setInCalendar(false);				
			}
			if($row['affiche']=='-1'){
				$document->setIsPublished(true);
			}else{
				$document->setIsPublished(false);				
			}
			//$document_files
			$filename= 'doc/' . $row['type'] . '/' . $row['pdf'];
			//$output->writeln('<comment>verfie pdf : ' . $filename . '</comment>');
			
			
			$em->persist($document);
			CreateMedia($row['pdf'], $row['type'], $em, $this->getApplication(), $output,  $row['titre'], $row['dt']);
			
			$media = $em->getRepository('ApplicationSonataMediaBundle:Media')->findOneByName($row['pdf']);
			
			$document->removeAllDocumentFile();
			//$output->writeln('<info>' . $row['medias_'.$j] . '</info>');
			if($media instanceof Media){	
				$doc_file = new DocumentFile();		
				$doc_file->setDocument($document);	
				$doc_file->setFile($media);	
				$doc_file->setPosition(1);
				$doc_file->setTitre($row['titre']);
				$em->persist($doc_file);	
				
				//$date = str_replace('/','-', $row['dt']);
				//$date_ = $media->getCreatedAt();
				
				/*$output->writeln('<comment>     date avant : ' . $media->getEnabled()  . '</comment>');
				$media->setEnabled(false);
				$em->persist($media);
				$output->writeln('<comment>     date apres : ' . $media->getEnabled()  . '</comment>');*/
				
			}	
			
			CreateMedia($row['pdf2'], $row['type'], $em, $this->getApplication(), $output,  'contenu', $row['dt']);
			$em->persist($document);			
			$media = $em->getRepository('ApplicationSonataMediaBundle:Media')->findOneByName($row['pdf2']);
			if($media instanceof Media){	
				$doc_file = new DocumentFile();		
				$doc_file->setDocument($document);	
				$doc_file->setFile($media);	
				$doc_file->setPosition(2);
				$doc_file->setTitre($row['pdf2_titre']);
				$em->persist($doc_file);	
			}	
			
			CreateMedia($row['lien_contenu'], $row['type'], $em, $this->getApplication(), $output,  'contenu', $row['dt']);
			$em->persist($document);			
			$media = $em->getRepository('ApplicationSonataMediaBundle:Media')->findOneByName($row['lien_contenu']);
			if($media instanceof Media){	
				$doc_file = new DocumentFile();		
				$doc_file->setDocument($document);	
				$doc_file->setFile($media);	
				$doc_file->setPosition(3);
				$doc_file->setTitre('contenu');
				$em->persist($doc_file);	
			}	
			
			$document->removeAllMedia();
			CreateMediaCouverture($row['media'], $row['type'], $em, $this->getApplication(), $output, $row['titre'], $row['dt']);
			$em->persist($document);			
			$media = $em->getRepository('ApplicationSonataMediaBundle:Media')->findOneByName($row['media']);
			if($media instanceof Media){	
				$document->setMedia($media);
				$em->persist($document);
			}	
			
			if (($i % $batchSize) === 0) {
				$em->flush();
				$em->clear();

				$progress->advance($batchSize);

				$now = new \DateTime();
				$output->writeln(' of documents imported ... | ' . $now->format('d-m-Y G:i:s'));
			}
			$i++;
		}
		$em->flush();
		$em->clear();

		// Ending the progress bar process
		$progress->finish();
		$output->writeln(' ');	
    }
}

function CreateMedia($media_name, $type, $em, $t, $output, $titre, $date){
		if(!(empty($media_name))){	
			//$filename= 'doc/' . $row['type'] . '/' . $row['pdf'];
			$media = $em->getRepository('ApplicationSonataMediaBundle:Media')->findOneByName($media_name);		
			
			//$output->writeln('<comment>' . get_class($media) . '</comment>');
			//App\Application\Sonata\MediaBundle\Entity\Media

			if(!($media instanceof Media)){
				//$date = (new \DateTime(str_replace('/','-', $row['dt'])));
				//$output->writeln('<comment>date=' . $date->format('Y-m-d H:i:s') . '</comment>');//excel->dd/mm/yyyy 		bdd->yyyy-mm-dd
				//$output->writeln('<comment>existe pas</comment>');
				$file = '/var/www/html/trigano-finance.com/public/uploads/import/doc/' . $type .'/' . $media_name ;
				$fs = new Filesystem();
				//php bin/console sonata:media:add sonata.media.provider.file pdf '/var/www/html/trigano-finance.com/public/uploads/import/doc/ope autre/17-01-AG-res-votes.pdf'

				try {
					// si le fichier existe dans repertoire IMPORT, on va executer la commande sonata_media
					if($fs->exists($file)){
							$command = $t->find('sonata:media:add');
							$arguments = array(
								'command' => 'sonata:media:add',
								'providerName'=> 'sonata.media.provider.file', 
								'context'=> 'pdf' , 
								'binaryContent'=> $file,
								'--description'=> $date
							);
							$greetInput = new ArrayInput($arguments);
							$output = new NullOutput();// rajouté pour ne pas avoir le texte de reponse
							$returnCode = $command->run($greetInput, $output);
						return true;
					}else{
						//$output->writeln('<comment>fichier n existe pas : ' . $file .'</comment>');
					}
				} catch (IOExceptionInterface $e) {
				} 
				
				
			}else{
				//$output->writeln('<comment>existe déjà</comment>');
				/*$media->setCreatedAt2(new \DateTime('2017-01-01 10:00:00'));
				$media->setDescription('retest');
				$em->persist($media);*/
			}
		}
		return false;
	}

function CreateMediaCouverture($media_name, $type, $em, $t, $output, $titre, $date){
		if(!(empty($media_name))){	
			$media = $em->getRepository('ApplicationSonataMediaBundle:Media')->findOneByName($media_name);	
			if(!($media instanceof Media)){
				//$output->writeln('<comment>existe pas</comment>');
				$file = '/var/www/html/trigano-finance.com/public/uploads/import/doc/' . $media_name ;
				$fs = new Filesystem();
				try {
					if($fs->exists($file)){
							$command = $t->find('sonata:media:add');
							$arguments = array(
								'command' => 'sonata:media:add',
								'providerName'=> 'sonata.media.provider.image', 
								'context'=> 'document' , 
								'binaryContent'=> $file,
								'--description'=> $date
							);
							$greetInput = new ArrayInput($arguments);
							$output = new NullOutput();// rajouté pour ne pas avoir le texte de reponse
							$returnCode = $command->run($greetInput, $output);
						return true;
					}else{
						$output->writeln('<comment>fichier n existe pas : ' . $file .'</comment>');
					}
				} catch (IOExceptionInterface $e) {} 
			}else{
				//$output->writeln('<comment>existe déjà</comment>');
			}
		}
		return false;
	}