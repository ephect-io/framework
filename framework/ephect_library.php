<?php

class EphectLibrary
{

    public static function mount()
    {

        $filenames = [
            "core_constants.php",

            "use_state.php",
            "use_slot.php",
            "use_effect.php",
            "use_query_argument.php",

            "logger_logger.php",
            "core_ini_loader_trait.php",
            "io_utils.php",
            "utils_zip.php",

            "core_structure_interface.php",
            "core_enumerator_interface.php",
            "objects_element_interface.php",
            "registry_objects_static_registry_interface.php",
            "registry_objects_abstract_registry_interface.php",
            "tree_tree_interface.php",
            "tasks_task_interface.php",
            "tasks_task_runner_interface.php",
            "cli_objects_phar_interface.php",
            "components_objects_component_interface.php",
            "components_objects_file_component_interface.php",
            "components_objects_children_interface.php",
            "components_objects_component_declaration_interface.php",
            "components_objects_component_entity_interface.php",
            "components_generators_objects_parser_service_interface.php",
            "components_generators_token_parsers_objects_token_parser_interface.php",

            "objects_element_trait.php",
            "objects_element_utils.php",
            "objects_static_element.php",
            "element.php",
            "core_phpinfo.php",
            "core_structure.php",
            "core_enumerator.php",
            "core_autoloader.php",
            "registry_objects_abstract_registry.php",
            "registry_objects_abstract_registry_contract.php",
            "registry_objects_abstract_static_registry_contract.php",
            "registry_objects_abstract_static_registry.php",

            "registry_state_registry.php",
            "registry_cache_registry.php",
            "registry_code_registry.php",
            "registry_framework_registry.php",
            "registry_registry.php",
    
            "registry_class_info.php",
            "registry_compoment_registry.php",
            "registry_route_registry.php",
            "registry_plugin_registry.php",
            "cache_cache.php",
            
            "web_curl.php",
            "tree_tree_iterator.php",
            "tree_tree.php",
            "crypto_crypto.php",
            "tasks_task.php",
            "tasks_task_runner.php",
            "tasks_task_structure.php",
            "core_objects_abstract_application.php",
   
            "cli_console_colors.php",
            "cli_application.php",
            "web_application.php",
            "xml_xml_utils.php",

            "components_objects_abstract_component.php",
            "components_objects_abstract_file_component.php",
            "components_objects_children.php",
            "components_objects_children_structure.php",
            "components_objects_component_declaration.php",
            "components_objects_component_declaration_structure.php",
            "components_objects_component_structure.php",
            "components_objects_component_entity.php",
            "components_component.php",
            "components_plugin.php",
            "components_objects_component_factory.php",
            "components_builders_abstract_builder.php",
            "components_validators_props_validator.php",
            "components_generators_parser.php",
            "components_generators_component_parser.php",
            "components_generators_component_document.php",
            "components_generators_token_parsers_objects_abstract_token_parser.php",
            "components_generators_token_parsers_values_parser.php",
            "components_generators_token_parsers_php_parser.php",
            "components_generators_token_parsers_child_slots_parser.php",
            "components_generators_token_parsers_uses_parser.php",
            "components_generators_token_parsers_useslot_parser.php",
            "components_generators_token_parsers_useeffect_parser.php",
            "components_generators_token_parsers_usevariables_parser.php",
            "components_generators_token_parsers_children_declaration_parser.php",
            "components_generators_token_parsers_html_parser.php",
            "components_generators_token_parsers_open_components_parser.php",
            "components_generators_token_parsers_echo_parser.php",
            "components_generators_token_parsers_arrays_parser.php",
            "components_generators_token_parsers_usesas_parser.php",
            "components_generators_token_parsers_mother_slots_parser.php",
            "components_generators_token_parsers_fragments_parser.php",
            "components_generators_token_parsers_arguments_parser.php",
            "components_generators_token_parsers_closed_components_parser.php",
            "components_generators_token_parsers_namespace_parser.php",
            "components_generators_parser_service.php",

            "components_compiler.php",

        ];

        if (Phar::running() != '') {
            foreach ($filenames as $filename) {
                include $filename;
            }
        } else {
        }
    }
}

EphectLibrary::mount();
