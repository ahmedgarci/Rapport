<?php

namespace App\Controller;

use App\Entity\Clients;
use App\Entity\DBSource;
use App\Entity\Rapports;
use App\Repository\RapportsRepository;
use App\Repository\TechnicienRepository;
use PHPJasper\PHPJasper;
use App\Repository\ClientsRepository;
use App\Repository\DBSourceRepository;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use App\Helpers;
use Doctrine\ORM\EntityManagerInterface;

class DBreportsController extends AbstractController
{
    

    /**
     * @Route("/Client/DemandeRapport", name="dbdemande", methods={"POST"})
     */
    public function Save_Report_To_Do(Request $request,JWTEncoderInterface $jwtEncoder,
    TechnicienRepository $techRepo,SerializerInterface $serializer,Helpers $helpers,
    DBSourceRepository $dbSourceRep, ClientsRepository $clientRep): JsonResponse
    {
        $token = $request->cookies->get("user");
        if(!$token){
            return new JsonResponse('Unauthorized',Response::HTTP_UNAUTHORIZED);            
        }
        $requestData = json_decode($request->getContent(),true);
        $technicien = $techRepo->findOneBy(["email"=> $requestData["emailTech"]]);
        if(!$technicien){
            return new JsonResponse('Technicien n existe pas !',Response::HTTP_NOT_FOUND);            
        }
        $ClientId = $helpers->DecodeToken($token);
        try {
            $client = $clientRep->findOneBy(["id"=>$ClientId]);
            $rapport = new DBSource();
            $rapport->setDriver($requestData["driver"])
                ->setUsername($requestData["username"])
                ->setPassword($requestData["password"])
                ->setHost($requestData["host"])
                ->setDB($requestData["DB"])
                ->setClient($client)
                ->setTableDesired($requestData['table'])
                ->setIsGenerated(false)
                ->setColumn1(isset($requestData["columns"][1]) ? $requestData["columns"][1] :null)
                ->setColumn2(isset($requestData["columns"][2]) ? $requestData["columns"][2] :null)
                ->setColumn3(isset($requestData["columns"][3]) ? $requestData["columns"][3] :null)
                ->setTech($technicien)
                ->setConditions($requestData['condition'])
                ->setOperateur($requestData['operateur'])
                ->setFieldChoosedFroCondition($requestData['field']);
            $dbSourceRep->add($rapport, true);
            return new JsonResponse('Demande Envoyée');
        } catch (\Exception $e) {
            return new JsonResponse('Error : ' . $e->getMessage());
        }
    }


    /**
     * @Route("/Techniciens/GenererRapportAvecDB", name="dbreport", methods={"POST"})
     */
    public function genererRapport(ClientsRepository $client, RapportsRepository $rap, 
    EntityManagerInterface $em , Request $request ,Helpers $helpers,DBSourceRepository $dbSourceRep,
    TechnicienRepository $technicien): Response
    {
            require __DIR__. '/../../vendor/autoload.php';
            $input = __DIR__ . '/../../vendor/geekcom/phpjasper/examples/hello_world.jrxml';
            $newFilename = uniqid();
            $output = __DIR__. '/../../public/reports/' . $newFilename;
            $DemandInfo = json_decode($request->getContent(), true)["DBInfo"];
            $tech = $technicien->find($helpers->DecodeToken($request->cookies->get("user")));
            $databaseOptions = [
             'driver' => $DemandInfo["driver"],
             'username' => $DemandInfo["username"],
             'password' => $DemandInfo["password"],
             'host' => $DemandInfo["host"],
             'database' => $DemandInfo["DB"]
         ];
    
            $champ1 = $DemandInfo['Column1'];
            $champ2 = $DemandInfo['Column2'];
            $champ3 = $DemandInfo['Column3'] ? $DemandInfo['Column3'] : null ;
            $table = $DemandInfo['TableDesired'];            
            $comp = $DemandInfo['fieldChoosedFroCondition'];
            $operateur = $DemandInfo['operateur'];
            $condition = $DemandInfo["Conditions"];
            

            if (empty($champ2) && empty($champ3) &&  empty($operateur) &&  empty($condition)) {
                $requette = "SELECT $champ1 FROM $table";
                $fields = [
                    ['name' => $champ1, 'class' => 'java.lang.String'],
                ];
            }
            elseif (empty($champ3) &&  empty($operateur) &&  empty($condition)) {
                $requette = "SELECT $champ1, $champ3 FROM $table";
                $fields = [
                    ['name' => $champ1, 'class' => 'java.lang.String'],
                    ['name' => $champ2, 'class' => 'java.lang.String'],
                ];
            }
            elseif (empty($operateur) &&  empty($condition)) {
                $requette = "SELECT $champ1, $champ2, $champ3 FROM $table";
                $fields = [
                    ['name' => $champ1, 'class' => 'java.lang.String'],
                    ['name' => $champ2, 'class' => 'java.lang.String'],
                    ['name' => $champ3, 'class' => 'java.lang.String'],
                ];
            }
            elseif(empty($champ2) &&  empty($champ3)) {
                $requette = "SELECT $champ1 FROM $table WHERE $comp $operateur '$condition'";
                $fields = [
                    ['name' => $champ1, 'class' => 'java.lang.String'],
                ];
            }elseif(empty($champ3)){
                $requette = "SELECT $champ1, $champ2 FROM $table WHERE $comp $operateur '$condition'";
                $fields = [
                    ['name' => $champ1, 'class' => 'java.lang.String'],
                    ['name' => $champ2, 'class' => 'java.lang.String'],
                ];
            }else{
                $requette = "SELECT $champ1, $champ2, $champ3 FROM $table WHERE $comp $operateur '$condition'";
                $fields = [
                    ['name' => $champ1, 'class' => 'java.lang.String'],
                    ['name' => $champ2, 'class' => 'java.lang.String'],
                    ['name' => $champ3, 'class' => 'java.lang.String'],
                ];
            }
            generateJrxml($fields);
    
    
            $options = [
                'format' => ['pdf'],
                'locale' => 'en',
                'db_connection' => $databaseOptions,
                'params' => [
                    'NomDuClient' => "Saidani Hazem",
                    'EmailDuClient' => "SaidaniHazem022@gmail.com",
                    'NomDuTechnicien' => "Garci Ahmed",
                    'requette' => $requette
                ]];
    
            $jasper = new PHPJasper;
            try {
                $jasper->process($input, $output, $options)->execute();
                $client = $client->findOneBy(["email"=>$DemandInfo["client"]["email"]]);
                $rapport = new Rapports();
                $rapport->setClient($client)
                ->setDate(new \DateTime("now", new \DateTimeZone("Africa/Tunis")))
                ->setReportPath($newFilename.".pdf")
                ->settitle("Report")->setTech($tech);
                $rap->add($rapport,true);
                $ClientDbsource = $dbSourceRep->findOneBy(["id"=>$DemandInfo["id"]]);
                $ClientDbsource->setIsGenerated(true);
                $em->persist($ClientDbsource);
                $em->flush();
                return new JsonResponse("Generated");
             } catch (\Exception $e) {
                 return new JsonResponse('error'. $e->getMessage(), 500);
             }
    }
}
    function generateJrxml($fields)
{
    $jrxml = '<?xml version="1.0" encoding="UTF-8"?>
    <jasperReport xmlns="http://jasperreports.sourceforge.net/jasperreports"
            xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
                xsi:schemaLocation="http://jasperreports.sourceforge.net/jasperreports http://jasperreports.sourceforge.net/xsd/jasperreport.xsd"
                name="Data_Report"
                language="groovy"
                pageWidth="595"
                pageHeight="842"
                columnWidth="535"
                leftMargin="20"
                rightMargin="20"
                topMargin="20"
                bottomMargin="20"
                uuid="527ae3c1-c10e-4b41-b983-14305308c942">
    
        <property name="ireport.zoom" value="1.5"/>
        <property name="ireport.x" value="0"/>
        <property name="ireport.y" value="0"/>
        
    
        <parameter name="NomDuClient" class="java.lang.String" />
        <parameter name="EmailDuClient" class="java.lang.String" />
        <parameter name="NomDuTechnicien" class="java.lang.String" />
        <parameter name="requette" class="java.lang.String"/>
    
        <queryString>
            <![CDATA[$P!{requette}]]>
        </queryString>';

    foreach ($fields as $field) {
        $jrxml .= '<field name="' . $field['name'] . '" class="' . $field['class'] . '"/>';
    }

    $jrxml .= '<background>
    <band/>
</background>
<title>
    <band height="72">
        <frame>
            <reportElement mode="Opaque" x="-20" y="-20" width="595" height="60" backcolor="#efffaf"
                    uuid="3501dac6-be9b-47b1-bf09-8b25fbc6c79f"/>
            <textField>
                <reportElement x="20" y="20" width="349" height="45" forecolor="#000000"
                        uuid="2464c9ca-82a1-48c9-87ea-b68192294c4a"/>
                <textElement>
                    <font fontName="Arial" size="18" isBold="true"/>
                </textElement>
                <textFieldExpression><![CDATA[$P{NomDuClient}]]></textFieldExpression>
            </textField>

            <textField>
                <reportElement x="20" y="50" width="349" height="45" forecolor="#000000"
                        uuid="2464c9ca-82a1-48c9-87ea-b68192294c4a"/>
                <textElement>
                    <font fontName="Arial" size="12" isBold="false"/>
                </textElement>
                <textFieldExpression><![CDATA[$P{EmailDuClient}]]></textFieldExpression>
            </textField>

            <textField>
                <reportElement x="245" y="25" width="349" height="20" forecolor="#000000" uuid="c15d0e17-2850-4010-b65a-e4822a371ba3"/>
                <textElement>
                    <font fontName="Arial" size="12" isBold="true"/>
                </textElement>
                <textFieldExpression><![CDATA["Date et Heure : " + new java.util.Date().toString()]]></textFieldExpression>
            </textField>
        
            <textField>
                <reportElement x="245" y="50" width="349" height="20" forecolor="#000000" uuid="c15d0e17-2850-4010-b65a-e4822a371ba3"/>
                <textElement>
                    <font fontName="Arial" size="12" isBold="true"/>
                </textElement>
                <textFieldExpression><![CDATA["Rapport Généré Par : " + $P{NomDuTechnicien}]]></textFieldExpression>
            </textField>
        </frame>
        </band>
        </title>

        <detail>
            <band height="20">';

    $xPosition = 0;
    foreach ($fields as $field) {
        $jrxml .=
            '<textField>
        <reportElement x="' . $xPosition . '" y="0" width="100" height="20"/>
        <textFieldExpression><![CDATA[$F{' . $field['name'] . '}]]></textFieldExpression>
        </textField>';
        $xPosition += 60;
    }
    $jrxml .= '
            </band>
            </detail>
            <pageFooter>
            <band height="17">
                <textField>
                    <reportElement mode="Opaque" x="0" y="4" width="515" height="13" backcolor="#E6E6E6"
                    uuid="470071d6-9789-41e5-b8f6-3e4340cc0ab2"/>
                    <textElement textAlignment="Right"/>
                    <textFieldExpression><![CDATA["Page "+$V{PAGE_NUMBER}+" of"]]></textFieldExpression>
                </textField>
                <textField evaluationTime="Report">
                    <reportElement mode="Opaque" x="515" y="4" width="40" height="13" backcolor="#E6E6E6"
                            uuid="f64278b8-d5b7-41e6-a40d-0e8929c0b848"/>
                    <textFieldExpression><![CDATA[" " + $V{PAGE_NUMBER}]]></textFieldExpression>
                </textField>
                <textField pattern="EEEEE dd MMMMM yyyy">
                    <reportElement x="0" y="4" width="100" height="13" uuid="2696db9f-481e-441c-8557-40163e951201"/>
                    <textFieldExpression><![CDATA[new java.util.Date()]]></textFieldExpression>
                </textField>
            </band>
            </pageFooter>
            </jasperReport>';

    $filePath = __DIR__ . '/../../vendor/geekcom/phpjasper/examples/hello_world.jrxml';
    file_put_contents($filePath, $jrxml);

    return $filePath;

}

