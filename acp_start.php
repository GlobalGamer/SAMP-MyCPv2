<?php

$currentPage = "acp_start.php";
include("global.php");

$config = getConfig();
if ($config["last_updatecheck"] + 60 * 60 * 12 < time())
{
    saveConfig(array("last_updatecheck" => time()));
    flush();
    $response = @file_get_contents($updateserver . "/versions/?version=" . $version);
    $response = json_decode($response);
    if ($response->code == "UPGRADE_SUGGESTED")
    {
        $config["update_available"] = "Es ist ein Update auf " . $response->version . " verfügbar.\n<br>";
        $config["update_available"] .= "Folgende Änderungen sind in der Version enthalten: \n";
        $config["update_available"] .= nl2br($response->changes);
        $config["update_available"] .= "Klicken Sie <a>hier</a> um das Update durchzuführen.";
    }
    elseif ($response->code == "NO_UPDATE")
    {
        $config["update_available"] = "myCP ist auf der allerneuesten Version $version.";
    }
    else
    {
        $config["update_available"] = "Es konnte keine Verbindung zum Updateserver hergestellt werden.";
    }
    saveConfig(array("update_available" => $config["update_available"]));
}

require_admin();
$title = "Start";
include_once("templates/header_acp.php");
?>
    <div class="row">
        <div class="col-lg-3 col-md-6">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <div class="row">
                        <div class="col-xs-3">
                            <i class="fa fa-users fa-5x"></i>
                        </div>
                        <div class="col-xs-9 text-right">
                            <div class="huge"><?php
                                $queryHandle = $db->getFirst("SELECT COUNT(*) AS count FROM !accounts");
                                echo $queryHandle["count"] ?></div>
                            <div>Benutzer</div>
                        </div>
                    </div>
                </div>
                <a href="#">
                    <div class="panel-footer">
                        <a href="acp_user.php">
                            <span class="pull-left">Details ansehen</span>
                            <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>

                            <div class="clearfix"></div>
                        </a>
                    </div>
                </a>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="panel panel-green">
                <div class="panel-heading">
                    <div class="row">
                        <div class="col-xs-3">
                            <i class="fa fa-paper-plane fa-5x"></i>
                        </div>
                        <div class="col-xs-9 text-right">
                            <div
                                class="huge"><?php
                                $queryHandle = $db->getFirst("SELECT (SELECT COUNT(*) FROM leader_applications, supporter_applications) as count_1, (SELECT COUNT(*) FROM leader_applications, supporter_applications) as count_2");
                                echo $queryHandle["count_1"] + $queryHandle["count_2"]; ?></div>
                            <div>offene Bewerbungen</div>
                        </div>
                    </div>
                </div>
                <a href="acp_applications.php">
                    <div class="panel-footer">
                        <span class="pull-left">Details ansehen</span>
                        <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>

                        <div class="clearfix"></div>
                    </div>
                </a>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="panel panel-yellow">
                <div class="panel-heading">
                    <div class="row">
                        <div class="col-xs-3">
                            <i class="fa fa-support fa-5x"></i>
                        </div>
                        <div class="col-xs-9 text-right">
                            <div
                                class="huge"><?php
                                $queryHandle = $db->getFirst("SELECT COUNT(*) AS count FROM support_tickets WHERE status != '2'");
                                $count = $queryHandle["count"];
                                echo $count; ?></div>
                            <div>offene Supporttickets</div>
                        </div>
                    </div>
                </div>
                <a href="acp_support.php">
                    <div class="panel-footer">
                        <span class="pull-left">Details ansehen</span>
                        <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>

                        <div class="clearfix"></div>
                    </div>
                </a>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="panel panel-red">
                <div class="panel-heading">
                    <div class="row">
                        <div class="col-xs-3">
                            <i class="fa fa-bullhorn fa-5x"></i>
                        </div>
                        <div class="col-xs-9 text-right">
                            <div
                                class="huge"><?php
                                $queryHandle = $db->getFirst("SELECT COUNT(*) AS count FROM complaints WHERE status != '2'");
                                $count = $queryHandle["count"];
                                echo $count; ?></div>
                            <div>offene Beschwerden</div>
                        </div>
                    </div>
                </div>
                <a href="acp_complaints.php">
                    <div class="panel-footer">
                        <span class="pull-left">Details ansehen</span>
                        <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>

                        <div class="clearfix"></div>
                    </div>
                </a>
            </div>
        </div>
    </div>
    <br/>
    <div class="col-lg-4">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><i class="fa fa-flag fa-fw"></i> Übersicht der Teammitglieder</h3>
            </div>
            <div class="panel-body">
                <div class="panel panel-default">
                    <div class="panel-heading" role="tab" id="headingOne">
                        <h4 class="panel-title">
                            <a data-toggle="collapse" data-parent="#accordion" href="#collapseOne" aria-expanded="true"
                               aria-controls="collapseOne">
                                Teammitglieder mit Zugang zum Dashboard <span class="glyphicon glyphicon-chevron-down"
                                                                              aria-hidden="true"></span>
                            </a>
                        </h4>
                    </div>
                    <div id="collapseOne" class="panel-collapse collapse in" role="tabpanel"
                         aria-labelledby="headingOne">
                        <div class="panel-body">
                            <?php
                            $result = $db->getAll("SELECT !username as username, !lastlogin as lastlogin FROM !accounts WHERE !ucp_adminrights != 0");
                            echo "<table class='table table-bordered table-hover'>";
                            echo "<thead>";
                            echo "<tr>";
                            echo "<td>&nbsp;&nbsp;&nbsp;<b>Benutzername</b>&nbsp;&nbsp;&nbsp;</td>";
                            echo "<td>&nbsp;&nbsp;&nbsp;<b>letztes Login</b>&nbsp;&nbsp;&nbsp;</td>";
                            echo "</tr>";
                            echo "</thead>";
                            foreach ($result as $row)
                            {
                                echo "</tbody>";
                                echo "<tr>";
                                echo "<td>", $row["username"], "</td>";
                                echo "<td>", $row["lastlogin"], "</td>";
                                echo "</tr>";
                                echo "</tbody>";
                            }
                            echo "</table>";
                            ?>
                        </div>
                    </div>
                </div>
                <div class="panel panel-default">
                    <div class="panel-heading" role="tab" id="headingTwo">
                        <h4 class="panel-title">
                            <a class="collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapseTwo"
                               aria-expanded="false" aria-controls="collapseTwo">
                                reguläre Teammitglieder <span class="glyphicon glyphicon-chevron-down"
                                                              aria-hidden="true"></span>
                            </a>
                        </h4>
                    </div>
                    <div id="collapseTwo" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingTwo">
                        <div class="panel-body">
                            <?php
                            $queryHandle = $db->getFirst("SELECT COUNT(*) AS count FROM !accounts WHERE !adminrights != 0");
                            $count = $queryHandle["count"];
                            if ($count == 0)
                            {
                                echo "<div class='alert alert-danger' role='alert'>";
                                echo "<strong>Hinweis:</strong> Derzeit gibt es keine regulären Teammitglieder.";
                                echo "</div>";
                            }
                            else
                            {
                                $result = $db->getAll("SELECT !username as username, !lastlogin as lastlogin FROM !accounts WHERE !adminrights != 0");
                                echo "<table class='table table-bordered table-hover'>";
                                echo "<thead>";
                                echo "<tr>";
                                echo "<td>&nbsp;&nbsp;&nbsp;<b>Benutzername</b>&nbsp;&nbsp;&nbsp;</td>";
                                echo "<td>&nbsp;&nbsp;&nbsp;<b>letztes Login</b>&nbsp;&nbsp;&nbsp;</td>";
                                echo "</tr>";
                                echo "</thead>";
                                foreach ($result as $row)
                                {
                                    echo "</tbody>";
                                    echo "<tr>";
                                    echo "<td>", $row["username"], "</td>";
                                    echo "<td>", $row["lastlogin"], "</td>";
                                    echo "</tr>";
                                    echo "</tbody>";
                                }
                                echo "</table>";
                            }
                            ?>
                        </div>
                    </div>
                </div>
                <div class="text-right">
                    <a href="acp_user.php">Details anzeigen <i class="fa fa-arrow-circle-right"></i></a>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><i class="fa fa-suitcase fa-fw"></i> Übersicht der Leader</h3>
            </div>
            <div class="panel-body">
                <?php
                $factions = $db->getFirst("SELECT * FROM factions");

                $queryHandle = $db->getFirst("SELECT COUNT(*) AS count FROM !accounts WHERE !leader != 0");
                $count = $queryHandle["count"];
                if ($count == 0)
                {
                    echo "<div class='alert alert-danger' role='alert'>";
                    echo "<strong>Hinweis:</strong> Derzeit gibt es keine Leader.";
                    echo "</div>";
                }
                else
                {
                    $result = $db->getAll("SELECT !username as username, !faction as faction FROM !accounts WHERE !leader != 0");
                    echo "<table class='table table-bordered table-hover'>";
                    echo "<thead>";
                    echo "<tr>";
                    echo "<td>&nbsp;&nbsp;&nbsp;<b>Benutzername</b>&nbsp;&nbsp;&nbsp;</td>";
                    echo "<td>&nbsp;&nbsp;&nbsp;<b>Leader der Fraktion</b>&nbsp;&nbsp;&nbsp;</td>";
                    echo "</tr>";
                    echo "</thead>";
                    foreach ($result as $row)
                    {
                        echo "<tr>";
                        echo "<td>", $row["username"], "</td>";
                        echo "<td>";
                        echo $factions["faction_" . $row["faction"]];

                        echo "</td>";
                        echo "</tr>";
                        echo "</tbody>";
                    }
                    echo "</table>";
                }
                ?>
                <div class="text-right">
                    <a href="acp_user.php">Details anzeigen <i class="fa fa-arrow-circle-right"></i></a>
                </div>
            </div>
        </div>
    </div>
<div class="col-lg-4">
    <div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title"><i class="glyphicon glyphicon-save"></i> myCP Updates</h3>
    </div>
    <div class="panel-body">
        <div class="table-responsive">
            <?= $config["update_available"] ?>
        </div>
        <div class="text-right">
            <a href="acp_update.php">Update durchführen <i class="fa fa-arrow-circle-right"></i></a>
        </div>
    </div>
</div>
<div>
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title"><i class="glyphicon glyphicon-comment"></i> myCP Support</h3>
        </div>
        <div class="panel-body">
            <div class="table-responsive">
                <!-- Place this tag where you want the Live Helper Plugin to render. -->
                <div id="lhc_status_container_page" ></div>

                <!-- Place this tag after the Live Helper Plugin tag. -->
                <script type="text/javascript">
                    var LHCChatOptionsPage = {};
                    LHCChatOptionsPage.opt = {};
                    (function() {
                        var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
                        po.src = '//support.mycp.xyz/index.php/chat/getstatusembed/(theme)/2';
                        var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
                    })();
                </script>
            </div>
            <div class="text-right">
                <a href="http://support.mycp.xyz/" target="_blank">direkt zum myCP Support <i class="fa fa-arrow-circle-right"></i></a>
            </div>
        </div>
</div>
<?php include_once("templates/footer_acp.php");