(function(){
    var setupController = function($scope, setupFactory, errorService, mainService){
        $scope.srps_vrednost = {};
        $scope.bonifikacija = {};

        setupFactory.getSetupParams().success(function(msg){
            console.log(msg);
            $scope.srps_vrednost = {
                'psenica_vlaga' : msg.srps_parametri.psenica_vlaga,
                'psenica_primese': msg.srps_parametri.psenica_primese,
                'psenica_hektolitar' : msg.srps_parametri.psenica_hektolitar,
                'jecam_vlaga' : msg.srps_parametri.jecam_vlaga,
                'jecam_primese': msg.srps_parametri.jecam_primese,
                'jecam_hektolitar' : msg.srps_parametri.jecam_hektolitar,
                'uljana_vlaga' : msg.srps_parametri.uljana_vlaga,
                'uljana_primese': msg.srps_parametri.uljana_primese,
                'suncokret_vlaga' : msg.srps_parametri.suncokret_vlaga,
                'suncokret_primese': msg.srps_parametri.suncokret_primese,
                'soja_vlaga' : msg.srps_parametri.soja_vlaga,
                'soja_primese': msg.srps_parametri.soja_primese,
                'kukuruz_vlaga' : msg.srps_parametri.kukuruz_vlaga,
                'kukuruz_primese': msg.srps_parametri.kukuruz_primese,
                'kukuruz_lom' : msg.srps_parametri.kukuruz_lom,
                'kukuruz_defekt': msg.srps_parametri.kukuruz_defekt
            };
            $scope.bonifikacija = {
                'psenica_donja_vlaga' : msg.bonifikacija.donja_vlps,
                'psenica_gornja_vlaga' : msg.bonifikacija.gornja_vlps,
                'psenica_donja_primesa': msg.bonifikacija.donja_prps,
                'psenica_gornja_primesa' : msg.bonifikacija.gornja_prps,
                'psenica_donja_hektolitar': msg.bonifikacija.donja_pshl_bo,
                'psenica_gornja_hektolitar' : msg.bonifikacija.gornja_pshl_bo,
                'jecam_donja_vlaga' : msg.bonifikacija.jecam_donja_vlaga,
                'jecam_gornja_vlaga' : msg.bonifikacija.jecam_gornja_vlaga,
                'jecam_donja_primesa': msg.bonifikacija.jecam_donja_primesa,
                'jecam_gornja_primesa' : msg.bonifikacija.jecam_gornja_primesa,
                'jecam_donja_hektolitar': msg.bonifikacija.jecam_donja_hektolitar,
                'jecam_gornja_hektolitar' : msg.bonifikacija.jecam_gornja_hektolitar,
                'uljana_donja_vlaga' : msg.bonifikacija.donja_uljvl,
                'uljana_gornja_vlaga' : msg.bonifikacija.gornja_uljvl,
                'uljana_donja_primesa': msg.bonifikacija.donja_uljpr,
                'uljana_gornja_primesa' : msg.bonifikacija.gornja_uljpr,
                'suncokret_donja_vlaga' : msg.bonifikacija.donja_sunvl,
                'suncokret_gornja_vlaga' : msg.bonifikacija.gornja_sunvl,
                'suncokret_donja_primesa': msg.bonifikacija.donja_sunpr,
                'suncokret_gornja_primesa' : msg.bonifikacija.gornja_sunpr,
                'soja_donja_vlaga' : msg.bonifikacija.donja_sovl,
                'soja_gornja_vlaga' : msg.bonifikacija.gornja_sovl,
                'soja_donja_primesa': msg.bonifikacija.donja_sopr,
                'soja_gornja_primesa' : msg.bonifikacija.gornja_sopr,
                'kukuruz_donja_vlaga' : msg.bonifikacija.donja_kuvl,
                'kukuruz_gornja_vlaga' : msg.bonifikacija.gornja_kuvl,
                'kukuruz_donja_primesa': msg.bonifikacija.donja_kupr,
                'kukuruz_gornja_primesa' : msg.bonifikacija.gornja_kupr,
                'kukuruz_donja_lom' : msg.bonifikacija.donja_kulo,
                'kukuruz_gornja_lom' : msg.bonifikacija.gornja_kulo,
                'kukuruz_donja_defekt': msg.bonifikacija.donja_kude,
                'kukuruz_gornja_defekt' : msg.bonifikacija.gornja_kude
            };
            $scope.ktabela = {
                'ku14' : msg.tabela_kukuruz.ku14,
                'ku14_5' : msg.tabela_kukuruz.ku14_5,
                'ku15': msg.tabela_kukuruz.ku15,
                'ku15_5' : msg.tabela_kukuruz.ku15_5,
                'ku16': msg.tabela_kukuruz.ku16,
                'ku16_5' : msg.tabela_kukuruz.ku16_5,
                'ku17' : msg.tabela_kukuruz.ku17,
                'ku17_5' : msg.tabela_kukuruz.ku17_5,
                'ku18': msg.tabela_kukuruz.ku18,
                'ku18_5' : msg.tabela_kukuruz.ku18_5,
                'ku19': msg.tabela_kukuruz.ku19,
                'ku19_5' : msg.tabela_kukuruz.ku19_5,
                'ku20' : msg.tabela_kukuruz.ku20,
                'ku20_5' : msg.tabela_kukuruz.ku20_5,
                'ku21': msg.tabela_kukuruz.ku21,
                'ku21_5' : msg.tabela_kukuruz.ku21_5,
                'ku22' : msg.tabela_kukuruz.ku22,
                'ku22_5' : msg.tabela_kukuruz.ku22_5,
                'ku23': msg.tabela_kukuruz.ku23,
                'ku23_5' : msg.tabela_kukuruz.ku23_5,
                'ku24' : msg.tabela_kukuruz.ku24,
                'ku24_5' : msg.tabela_kukuruz.ku24_5,
                'ku25': msg.tabela_kukuruz.ku25,
                'ku25_5': msg.tabela_kukuruz.ku25_5,
                'ku26' : msg.tabela_kukuruz.ku26,
                'ku26_5' : msg.tabela_kukuruz.ku26_5,
                'ku27' : msg.tabela_kukuruz.ku27,
                'ku27_5': msg.tabela_kukuruz.ku27_5,
                'ku28' : msg.tabela_kukuruz.ku28,
                'ku28_5' : msg.tabela_kukuruz.ku28_5,
                'ku29' : msg.tabela_kukuruz.ku29,
                'ku29_5': msg.tabela_kukuruz.ku29_5
            };
            $scope.obracun = {
                psenica_obracun : msg.obracun_vlage.psenica_obracun,
                jecam_obracun: msg.obracun_vlage.jecam_obracun,
                uljana_obracun :  msg.obracun_vlage.uljana_obracun,
                suncokret_obracun:  msg.obracun_vlage.suncokret_obracun,
                soja_obracun:  msg.obracun_vlage.soja_obracun,
                kukuruz_obracun:  msg.obracun_vlage.kukuruz_obracun
            };
            $scope.ptabela = {
                ps14 : msg.tabela_psenica.ps14,
                ps14_50: msg.tabela_psenica.ps14_50,
                ps15 :  msg.tabela_psenica.ps15,
                ps15_50:  msg.tabela_psenica.ps15_50,
                ps16:  msg.tabela_psenica.ps16,
                ps16_50:  msg.tabela_psenica.ps16_50,
                ps17 : msg.tabela_psenica.ps17,
                ps17_50: msg.tabela_psenica.ps17_50,
                ps18 :  msg.tabela_psenica.ps18,
                ps18_50:  msg.tabela_psenica.ps18_50,
                ps19:  msg.tabela_psenica.ps19,
                ps19_50:  msg.tabela_psenica.ps19_50
            };
        }).error(function(error){
            alert('Veza sa serverom onemogućena. Mogući problemi\n1.Nema internet konekcije\n2.Radovi na serveru (molim vas sacekajte)');
            console.log(error);
        });



        $scope.updateSrps = function(){
            if(!$scope.srps_vrednost.hasOwnProperty('psenica_vlaga') || $scope.srps_vrednost.psenica_vlaga==="" || typeof $scope.srps_vrednost.psenica_vlaga === 'undefined'){
                errorService.error_msg($('input[name="psenica_vlaga"]'), "Polje za dodelu SRPS vrednosti vlage ne moze ostati prazno!"); return false;
            }
            if(!$scope.srps_vrednost.hasOwnProperty('psenica_primese') || $scope.srps_vrednost.psenica_primese==="" || typeof $scope.srps_vrednost.psenica_primese === 'undefined'){
                errorService.error_msg($('input[name="psenica_primese"]'), "Polje za dodelu SRPS vrednosti primese ne moze ostati prazno!"); return false;
            }
            if(!$scope.srps_vrednost.hasOwnProperty('psenica_hektolitar') || $scope.srps_vrednost.psenica_hektolitar==="" || typeof $scope.srps_vrednost.psenica_hektolitar === 'undefined'){
                errorService.error_msg($('input[name="psenica_hektolitar"]'), "Polje za dodelu SRPS vrednosti hektolitra ne moze ostati prazno!"); return false;
            }
            if(!$scope.srps_vrednost.hasOwnProperty('jecam_vlaga') || $scope.srps_vrednost.jecam_vlaga==="" || typeof $scope.srps_vrednost.jecam_vlaga === 'undefined'){
                errorService.error_msg($('input[name="jecam_vlaga"]'), "Polje za dodelu SRPS vrednosti vlage ne moze ostati prazno!"); return false;
            }
            if(!$scope.srps_vrednost.hasOwnProperty('jecam_primese') || $scope.srps_vrednost.jecam_primese==="" || typeof $scope.srps_vrednost.jecam_primese === 'undefined'){
                errorService.error_msg($('input[name="jecam_primese"]'), "Polje za dodelu SRPS vrednosti primese ne moze ostati prazno!"); return false;
            }
            if(!$scope.srps_vrednost.hasOwnProperty('psenica_hektolitar') || $scope.srps_vrednost.jecam_hektolitar==="" || typeof $scope.srps_vrednost.jecam_hektolitar === 'undefined'){
                errorService.error_msg($('input[name="jecam_hektolitar"]'), "Polje za dodelu SRPS vrednosti hektolitra ne moze ostati prazno!"); return false;
            }
            if(!$scope.srps_vrednost.hasOwnProperty('uljana_vlaga') || $scope.srps_vrednost.uljana_vlaga==="" || typeof $scope.srps_vrednost.uljana_vlaga === 'undefined'){
                errorService.error_msg($('input[name="uljana_vlaga"]'), "Polje za dodelu SRPS vrednosti vlage ne moze ostati prazno!"); return false;
            }
            if(!$scope.srps_vrednost.hasOwnProperty('uljana_primese') || $scope.srps_vrednost.uljana_primese==="" || typeof $scope.srps_vrednost.uljana_primese === 'undefined'){
                errorService.error_msg($('input[name="uljana_primese"]'), "Polje za dodelu SRPS vrednosti primese ne moze ostati prazno!"); return false;
            }
            if(!$scope.srps_vrednost.hasOwnProperty('suncokret_vlaga') || $scope.srps_vrednost.suncokret_vlaga==="" || typeof $scope.srps_vrednost.suncokret_vlaga === 'undefined'){
                errorService.error_msg($('input[name="suncokret_vlaga"]'), "Polje za dodelu SRPS vrednosti vlage ne moze ostati prazno!"); return false;
            }
            if(!$scope.srps_vrednost.hasOwnProperty('suncokret_primese') || $scope.srps_vrednost.suncokret_primese==="" || typeof $scope.srps_vrednost.suncokret_primese === 'undefined'){
                errorService.error_msg($('input[name="suncokret_primese"]'), "Polje za dodelu SRPS vrednosti primese ne moze ostati prazno!"); return false;
            }
            if(!$scope.srps_vrednost.hasOwnProperty('soja_vlaga') || $scope.srps_vrednost.soja_vlaga==="" || typeof $scope.srps_vrednost.soja_vlaga === 'undefined'){
                errorService.error_msg($('input[name="soja_vlaga"]'), "Polje za dodelu SRPS vrednosti vlage ne moze ostati prazno!"); return false;
            }
            if(!$scope.srps_vrednost.hasOwnProperty('soja_primese') || $scope.srps_vrednost.soja_primese==="" || typeof $scope.srps_vrednost.soja_primese === 'undefined'){
                errorService.error_msg($('input[name="soja_primese"]'), "Polje za dodelu SRPS vrednosti primese ne moze ostati prazno!"); return false;
            }
            if(!$scope.srps_vrednost.hasOwnProperty('kukuruz_vlaga') || $scope.srps_vrednost.kukuruz_vlaga==="" || typeof $scope.srps_vrednost.kukuruz_vlaga === 'undefined'){
                errorService.error_msg($('input[name="kukuruz_vlaga"]'), "Polje za dodelu SRPS vrednosti vlage ne moze ostati prazno!"); return false;
            }
            if(!$scope.srps_vrednost.hasOwnProperty('kukuruz_primese') || $scope.srps_vrednost.kukuruz_primese==="" || typeof $scope.srps_vrednost.kukuruz_primese === 'undefined'){
                errorService.error_msg($('input[name="kukuruz_primese"]'), "Polje za dodelu SRPS vrednosti primese ne moze ostati prazno!"); return false;
            }
            if(!$scope.srps_vrednost.hasOwnProperty('kukuruz_lom') || $scope.srps_vrednost.kukuruz_lom==="" || typeof $scope.srps_vrednost.kukuruz_lom === 'undefined'){
                errorService.error_msg($('input[name="kukuruz_lom"]'), "Polje za dodelu SRPS vrednosti loma ne moze ostati prazno!"); return false;
            }
            if(!$scope.srps_vrednost.hasOwnProperty('kukuruz_defekt') || $scope.srps_vrednost.kukuruz_defekt==="" || typeof $scope.srps_vrednost.kukuruz_defekt === 'undefined'){
                errorService.error_msg($('input[name="kukuruz_defekt"]'), "Polje za dodelu SRPS vrednosti defekta ne moze ostati prazno!"); return false;
            }
            $('#srps_form').find('.ajax_load_visibility').css({'visibility':'visible'});
            setupFactory.updateSrps($scope.srps_vrednost).success(function(msg){
                $('#srps_form').find('.ajax_load_visibility').css({'visibility':'hidden'});
                console.log(msg);

                $('#srps_message').css({'display':'block'})
                $('#srps_message').animate({'opacity':'1','top':'45%'}, 600, 'swing', function(){
                    $('#srps_message').delay(1000).animate({'opacity':'0','top':'80%'}, 600, 'swing', function(){//.delay(600)
                        $('#srps_message').css({'top':'30%','display':'none'});
                    });
                });
            }).error(function(error){
                alert('Veza sa serverom je onemogućena. Mogući problemi:\n1.Nema internet konekcije\n2.Radovi na serveru');
                console.log(error);
            });


        };

        $scope.updateBonifikacija = function(){
            if(!$scope.bonifikacija.hasOwnProperty('psenica_donja_vlaga') || $scope.bonifikacija.psenica_donja_vlaga==="" || typeof $scope.bonifikacija.psenica_donja_vlaga === 'undefined'){
                errorService.error_msg($('input[name="psenica_donja_vlaga"]'), "Polje za dodelu donje margine bonifikacije vlage ne moze ostati prazno!"); return false;
            }
            if(!$scope.bonifikacija.hasOwnProperty('psenica_gornja_vlaga') || $scope.bonifikacija.psenica_gornja_vlaga==="" || typeof $scope.bonifikacija.psenica_gornja_vlaga === 'undefined'){
                errorService.error_msg($('input[name="psenica_gornja_vlaga"]'), "Polje za dodelu gornje margine bonifikacije vlage ne moze ostati prazno!"); return false;
            }
            if(!$scope.bonifikacija.hasOwnProperty('psenica_donja_primesa') || $scope.bonifikacija.psenica_donja_primesa==="" || typeof $scope.bonifikacija.psenica_donja_primesa === 'undefined'){
                errorService.error_msg($('input[name="psenica_donja_primesa"]'), "Polje za dodelu donje margine bonifikacije primesa ne moze ostati prazno!"); return false;
            }
            if(!$scope.bonifikacija.hasOwnProperty('psenica_gornja_primesa') || $scope.bonifikacija.psenica_gornja_primesa==="" || typeof $scope.bonifikacija.psenica_gornja_primesa === 'undefined'){
                errorService.error_msg($('input[name="psenica_gornja_primesa"]'), "Polje za dodelu gornje margine bonifikacije primesa ne moze ostati prazno!"); return false;
            }
            if(!$scope.bonifikacija.hasOwnProperty('psenica_donja_hektolitar') || $scope.bonifikacija.psenica_donja_hektolitar==="" || typeof $scope.bonifikacija.psenica_donja_hektolitar === 'undefined'){
                errorService.error_msg($('input[name="psenica_donja_hektolitar"]'), "Polje za dodelu donje margine bonifikacije hektolitra ne moze ostati prazno!"); return false;
            }
            if(!$scope.bonifikacija.hasOwnProperty('psenica_gornja_hektolitar') || $scope.bonifikacija.psenica_gornja_hektolitar==="" || typeof $scope.bonifikacija.psenica_gornja_hektolitar === 'undefined'){
                errorService.error_msg($('input[name="psenica_gornja_hektolitar"]'), "Polje za dodelu gornje margine bonifikacije hektolitra ne moze ostati prazno!"); return false;
            }
            if(!$scope.bonifikacija.hasOwnProperty('jecam_donja_vlaga') || $scope.bonifikacija.jecam_donja_vlaga==="" || typeof $scope.bonifikacija.jecam_donja_vlaga === 'undefined'){
                errorService.error_msg($('input[name="jecam_donja_vlaga"]'), "Polje za dodelu donje margine bonifikacije vlage ne moze ostati prazno!"); return false;
            }
            if(!$scope.bonifikacija.hasOwnProperty('jecam_gornja_vlaga') || $scope.bonifikacija.jecam_gornja_vlaga==="" || typeof $scope.bonifikacija.jecam_gornja_vlaga === 'undefined'){
                errorService.error_msg($('input[name="jecam_gornja_vlaga"]'), "Polje za dodelu gornje margine bonifikacije vlage ne moze ostati prazno!"); return false;
            }
            if(!$scope.bonifikacija.hasOwnProperty('jecam_donja_primesa') || $scope.bonifikacija.jecam_donja_primesa==="" || typeof $scope.bonifikacija.jecam_donja_primesa === 'undefined'){
                errorService.error_msg($('input[name="jecam_donja_primesa"]'), "Polje za dodelu donje margine bonifikacije primesa ne moze ostati prazno!"); return false;
            }
            if(!$scope.bonifikacija.hasOwnProperty('jecam_gornja_primesa') || $scope.bonifikacija.jecam_gornja_primesa==="" || typeof $scope.bonifikacija.jecam_gornja_primesa === 'undefined'){
                errorService.error_msg($('input[name="jecam_gornja_primesa"]'), "Polje za dodelu gornje margine bonifikacije primesa ne moze ostati prazno!"); return false;
            }
            if(!$scope.bonifikacija.hasOwnProperty('jecam_donja_hektolitar') || $scope.bonifikacija.jecam_donja_hektolitar==="" || typeof $scope.bonifikacija.jecam_donja_hektolitar === 'undefined'){
                errorService.error_msg($('input[name="jecam_donja_hektolitar"]'), "Polje za dodelu donje margine bonifikacije hektolitra ne moze ostati prazno!"); return false;
            }
            if(!$scope.bonifikacija.hasOwnProperty('jecam_gornja_hektolitar') || $scope.bonifikacija.jecam_gornja_hektolitar==="" || typeof $scope.bonifikacija.jecam_gornja_hektolitar === 'undefined'){
                errorService.error_msg($('input[name="jecam_gornja_hektolitar"]'), "Polje za dodelu gornje margine bonifikacije hektolitra ne moze ostati prazno!"); return false;
            }
            if(!$scope.bonifikacija.hasOwnProperty('uljana_donja_vlaga') || $scope.bonifikacija.uljana_donja_vlaga==="" || typeof $scope.bonifikacija.uljana_donja_vlaga === 'undefined'){
                errorService.error_msg($('input[name="uljana_donja_vlaga"]'), "Polje za dodelu donje margine bonifikacije vlage ne moze ostati prazno!"); return false;
            }
            if(!$scope.bonifikacija.hasOwnProperty('uljana_gornja_vlaga') || $scope.bonifikacija.uljana_gornja_vlaga==="" || typeof $scope.bonifikacija.uljana_gornja_vlaga === 'undefined'){
                errorService.error_msg($('input[name="uljana_gornja_vlaga"]'), "Polje za dodelu gornje margine bonifikacije vlage ne moze ostati prazno!"); return false;
            }
            if(!$scope.bonifikacija.hasOwnProperty('uljana_donja_primesa') || $scope.bonifikacija.uljana_donja_primesa==="" || typeof $scope.bonifikacija.uljana_donja_primesa === 'undefined'){
                errorService.error_msg($('input[name="uljana_donja_primesa"]'), "Polje za dodelu donje margine bonifikacije primesa ne moze ostati prazno!"); return false;
            }
            if(!$scope.bonifikacija.hasOwnProperty('uljana_gornja_primesa') || $scope.bonifikacija.uljana_gornja_primesa==="" || typeof $scope.bonifikacija.uljana_gornja_primesa === 'undefined'){
                errorService.error_msg($('input[name="uljana_gornja_primesa"]'), "Polje za dodelu gornje margine bonifikacije primesa ne moze ostati prazno!"); return false;
            }
            if(!$scope.bonifikacija.hasOwnProperty('suncokret_donja_vlaga') || $scope.bonifikacija.suncokret_donja_vlaga==="" || typeof $scope.bonifikacija.suncokret_donja_vlaga === 'undefined'){
                errorService.error_msg($('input[name="suncokret_donja_vlaga"]'), "Polje za dodelu donje margine bonifikacije vlage ne moze ostati prazno!"); return false;
            }
            if(!$scope.bonifikacija.hasOwnProperty('suncokret_gornja_vlaga') || $scope.bonifikacija.suncokret_gornja_vlaga==="" || typeof $scope.bonifikacija.suncokret_gornja_vlaga === 'undefined'){
                errorService.error_msg($('input[name="suncokret_gornja_vlaga"]'), "Polje za dodelu gornje margine bonifikacije vlage ne moze ostati prazno!"); return false;
            }
            if(!$scope.bonifikacija.hasOwnProperty('suncokret_donja_primesa') || $scope.bonifikacija.suncokret_donja_primesa==="" || typeof $scope.bonifikacija.suncokret_donja_primesa === 'undefined'){
                errorService.error_msg($('input[name="suncokret_donja_primesa"]'), "Polje za dodelu donje margine bonifikacije primesa ne moze ostati prazno!"); return false;
            }
            if(!$scope.bonifikacija.hasOwnProperty('suncokret_gornja_primesa') || $scope.bonifikacija.suncokret_gornja_primesa==="" || typeof $scope.bonifikacija.suncokret_gornja_primesa === 'undefined'){
                errorService.error_msg($('input[name="suncokret_gornja_primesa"]'), "Polje za dodelu gornje margine bonifikacije primesa ne moze ostati prazno!"); return false;
            }
            if(!$scope.bonifikacija.hasOwnProperty('soja_donja_vlaga') || $scope.bonifikacija.soja_donja_vlaga==="" || typeof $scope.bonifikacija.soja_donja_vlaga === 'undefined'){
                errorService.error_msg($('input[name="soja_donja_vlaga"]'), "Polje za dodelu donje margine bonifikacije vlage ne moze ostati prazno!"); return false;
            }
            if(!$scope.bonifikacija.hasOwnProperty('soja_gornja_vlaga') || $scope.bonifikacija.soja_gornja_vlaga==="" || typeof $scope.bonifikacija.soja_gornja_vlaga === 'undefined'){
                errorService.error_msg($('input[name="soja_gornja_vlaga"]'), "Polje za dodelu gornje margine bonifikacije vlage ne moze ostati prazno!"); return false;
            }
            if(!$scope.bonifikacija.hasOwnProperty('soja_donja_primesa') || $scope.bonifikacija.soja_donja_primesa==="" || typeof $scope.bonifikacija.soja_donja_primesa === 'undefined'){
                errorService.error_msg($('input[name="soja_donja_primesa"]'), "Polje za dodelu donje margine bonifikacije primesa ne moze ostati prazno!"); return false;
            }
            if(!$scope.bonifikacija.hasOwnProperty('soja_gornja_primesa') || $scope.bonifikacija.soja_gornja_primesa==="" || typeof $scope.bonifikacija.soja_gornja_primesa === 'undefined'){
                errorService.error_msg($('input[name="soja_gornja_primesa"]'), "Polje za dodelu gornje margine bonifikacije primesa ne moze ostati prazno!"); return false;
            }
            if(!$scope.bonifikacija.hasOwnProperty('kukuruz_donja_vlaga') || $scope.bonifikacija.kukuruz_donja_vlaga==="" || typeof $scope.bonifikacija.kukuruz_donja_vlaga === 'undefined'){
                errorService.error_msg($('input[name="kukuruz_donja_vlaga"]'), "Polje za dodelu donje margine bonifikacije vlage ne moze ostati prazno!"); return false;
            }
            if(!$scope.bonifikacija.hasOwnProperty('kukuruz_gornja_vlaga') || $scope.bonifikacija.kukuruz_gornja_vlaga==="" || typeof $scope.bonifikacija.kukuruz_gornja_vlaga === 'undefined'){
                errorService.error_msg($('input[name="kukuruz_gornja_vlaga"]'), "Polje za dodelu gornje margine bonifikacije vlage ne moze ostati prazno!"); return false;
            }
            if(!$scope.bonifikacija.hasOwnProperty('kukuruz_donja_primesa') || $scope.bonifikacija.kukuruz_donja_primesa==="" || typeof $scope.bonifikacija.kukuruz_donja_primesa === 'undefined'){
                errorService.error_msg($('input[name="kukuruz_donja_primesa"]'), "Polje za dodelu donje margine bonifikacije primesa ne moze ostati prazno!"); return false;
            }
            if(!$scope.bonifikacija.hasOwnProperty('kukuruz_gornja_primesa') || $scope.bonifikacija.kukuruz_gornja_primesa==="" || typeof $scope.bonifikacija.kukuruz_gornja_primesa === 'undefined'){
                errorService.error_msg($('input[name="kukuruz_gornja_primesa"]'), "Polje za dodelu gornje margine bonifikacije primesa ne moze ostati prazno!"); return false;
            }
            if(!$scope.bonifikacija.hasOwnProperty('kukuruz_donja_lom') || $scope.bonifikacija.kukuruz_donja_lom==="" || typeof $scope.bonifikacija.kukuruz_donja_lom === 'undefined'){
                errorService.error_msg($('input[name="kukuruz_donja_lom"]'), "Polje za dodelu donje margine bonifikacije loma ne moze ostati prazno!"); return false;
            }
            if(!$scope.bonifikacija.hasOwnProperty('kukuruz_gornja_lom') || $scope.bonifikacija.kukuruz_gornja_lom==="" || typeof $scope.bonifikacija.kukuruz_gornja_lom === 'undefined'){
                errorService.error_msg($('input[name="kukuruz_gornja_lom"]'), "Polje za dodelu gornje margine bonifikacije loma ne moze ostati prazno!"); return false;
            }
            if(!$scope.bonifikacija.hasOwnProperty('kukuruz_donja_defekt') || $scope.bonifikacija.kukuruz_donja_defekt==="" || typeof $scope.bonifikacija.kukuruz_donja_defekt === 'undefined'){
                errorService.error_msg($('input[name="kukuruz_donja_defekt"]'), "Polje za dodelu donje margine bonifikacije defekta ne moze ostati prazno!"); return false;
            }
            if(!$scope.bonifikacija.hasOwnProperty('kukuruz_gornja_defekt') || $scope.bonifikacija.kukuruz_gornja_defekt==="" || typeof $scope.bonifikacija.kukuruz_gornja_defekt === 'undefined'){
                errorService.error_msg($('input[name="kukuruz_gornja_defekt"]'), "Polje za dodelu gornje margine bonifikacije defekta ne moze ostati prazno!"); return false;
            }

            $('#bonifikacija_form').find('.ajax_load_visibility').css({'visibility':'visible'});
            setupFactory.updateBonifikacija($scope.bonifikacija).success(function(msg){

                $('#bonifikacija_form').find('.ajax_load_visibility').css({'visibility':'hidden'});
                console.log(msg);
                if(!msg.hasOwnProperty('logout') && msg.logout!==0){
                    $('#bonifikacija_message').css({'display':'block'})
                    $('#bonifikacija_message').animate({'opacity':'1','top':'45%'}, 600, 'swing', function(){
                        $('#bonifikacija_message').delay(1000).animate({'opacity':'0','top':'80%'}, 600, 'swing', function(){//.delay(600)
                            $('#bonifikacija_message').css({'top':'30%','display':'none'});
                        });
                    });
                } else {
                    window.location.href = mainService.domainURL();
                }
            }).error(function(error){
                alert('Veza sa serverom je onemogućena. Mogući problemi:\n1.Nema internet konekcije\n2.Radovi na serveru');
                console.log(error);
            });


        };


        $scope.updateKukuruzTabela = function() {
            if($scope.kukuruz_error('ku14') !== false && $scope.kukuruz_error('ku14_5') !== false
                && $scope.kukuruz_error('ku15') !== false && $scope.kukuruz_error('ku15_5') !== false
                && $scope.kukuruz_error('ku16') !== false && $scope.kukuruz_error('ku16_5') !== false
                && $scope.kukuruz_error('ku17') !== false && $scope.kukuruz_error('ku17_5') !== false
                && $scope.kukuruz_error('ku18') !== false && $scope.kukuruz_error('ku18_5') !== false
                && $scope.kukuruz_error('ku19') !== false && $scope.kukuruz_error('ku19_5') !== false
                && $scope.kukuruz_error('ku20') !== false && $scope.kukuruz_error('ku20_5') !== false
                && $scope.kukuruz_error('ku21') !== false && $scope.kukuruz_error('ku21_5') !== false
                && $scope.kukuruz_error('ku22') !== false && $scope.kukuruz_error('ku22_5') !== false
                && $scope.kukuruz_error('ku23') !== false && $scope.kukuruz_error('ku23_5') !== false
                && $scope.kukuruz_error('ku24') !== false && $scope.kukuruz_error('ku24_5') !== false
                && $scope.kukuruz_error('ku25') !== false && $scope.kukuruz_error('ku25_5') !== false
                && $scope.kukuruz_error('ku26') !== false && $scope.kukuruz_error('ku26_5') !== false
                && $scope.kukuruz_error('ku27') !== false && $scope.kukuruz_error('ku27_5') !== false
                && $scope.kukuruz_error('ku28') !== false && $scope.kukuruz_error('ku28_5') !== false
                && $scope.kukuruz_error('ku29') !== false && $scope.kukuruz_error('ku29_5') !== false){
                console.log($scope.ktabela);
                $('#kukuruz_tabela_form').find('.ajax_load_visibility').css({'visibility':'visible'});
                setupFactory.updateKukuruzTabela($scope.ktabela).success(function(msg){
                    $('#kukuruz_tabela_form').find('.ajax_load_visibility').css({'visibility':'hidden'});
                    console.log(msg);

                    $('#kukuruz_tabela_message').css({'display':'block'})
                    $('#kukuruz_tabela_message').animate({'opacity':'1','top':'45%'}, 600, 'swing', function(){
                        $('#kukuruz_tabela_message').delay(1000).animate({'opacity':'0','top':'80%'}, 600, 'swing', function(){//.delay(600)
                            $('#kukuruz_tabela_message').css({'top':'30%','display':'none'});
                        });
                    });
                }).error(function(error){
                    alert('Veza sa serverom je onemogućena. Mogući problemi:\n1.Nema internet konekcije\n2.Radovi na serveru');
                    console.log(error);
                });
            }
        };

        $scope.updatePsenicaTabela = function() {
            if($scope.psenica_error('ps14') !== false && $scope.psenica_error('ps14_50') !== false
                && $scope.psenica_error('ps15') !== false && $scope.psenica_error('ps15_50') !== false
                && $scope.psenica_error('ps16') !== false && $scope.psenica_error('ps16_50') !== false
                && $scope.psenica_error('ps17') !== false && $scope.psenica_error('ps17_50') !== false
                && $scope.psenica_error('ps18') !== false && $scope.psenica_error('ps18_50') !== false
                && $scope.psenica_error('ps19') !== false && $scope.psenica_error('ps19_50') !== false){
                console.log($scope.ktabela);
                $('#psenica_tabela_form').find('.ajax_load_visibility').css({'visibility':'visible'});
                setupFactory.updatePsenicaTabela($scope.ptabela).success(function(msg){
                    $('#psenica_tabela_form').find('.ajax_load_visibility').css({'visibility':'hidden'});
                    console.log(msg);

                    $('#psenica_tabela_message').css({'display':'block'})
                    $('#psenica_tabela_message').animate({'opacity':'1','top':'45%'}, 600, 'swing', function(){
                        $('#psenica_tabela_message').delay(1000).animate({'opacity':'0','top':'80%'}, 600, 'swing', function(){//.delay(600)
                            $('#psenica_tabela_message').css({'top':'30%','display':'none'});
                        });
                    });
                }).error(function(error){
                    alert('Veza sa serverom je onemogućena. Mogući problemi:\n1.Nema internet konekcije\n2.Radovi na serveru');
                    console.log(error);
                });
            }
        };

        $scope.kukuruz_error = function(param) {
            if (!$scope.ktabela.hasOwnProperty(param) || $scope.ktabela[param] === "" || typeof $scope.ktabela[param] === 'undefined') {
                errorService.error_msg($('input[name="'+param+'"]'), "Polje ne moze ostati ne popunjeno!");
                return false;
            }
        };

        $scope.psenica_error = function(param) {
            if (!$scope.ptabela.hasOwnProperty(param) || $scope.ptabela[param] === "" || typeof $scope.ptabela[param] === 'undefined') {
                errorService.error_msg($('input[name="'+param+'"]'), "Polje ne moze ostati ne popunjeno!");
                return false;
            }
        };

        $scope.updateObracunVlage = function(){
            $('#obracun_vlage_form').find('.ajax_load_visibility').css({'visibility':'visible'});
            setupFactory.updateObracunVlage($scope.obracun).success(function(msg){
                $('#obracun_vlage_form').find('.ajax_load_visibility').css({'visibility':'hidden'});
                console.log(msg);
                if(!msg.hasOwnProperty('logout') && msg.logout!==0){
                    $('#obracun_vlage_message').css({'display':'block'})
                    $('#obracun_vlage_message').animate({'opacity':'1','top':'45%'}, 600, 'swing', function(){
                        $('#obracun_vlage_message').delay(1000).animate({'opacity':'0','top':'80%'}, 600, 'swing', function(){//.delay(600)
                            $('#obracun_vlage_message').css({'top':'30%','display':'none'});
                        });
                    });
                } else {
                    window.location.href = mainService.domainURL();
                }
            }).error(function(error){
                alert('Veza sa serverom je onemogućena. Mogući problemi:\n1.Nema internet konekcije\n2.Radovi na serveru');
                console.log(error);
            });

        }

    };
    setupController.$inject = ['$scope', 'setupFactory', 'errorService', 'mainService'];
    angular.module('_raiffisenApp').controller('setupController', setupController);

}());