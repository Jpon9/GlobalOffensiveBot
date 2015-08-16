import io, json, os, sys

metadata_path = os.path.dirname(os.path.abspath(sys.argv[0])) + "/cache/metadata.json";

def GetMetadata():
	if not os.path.exists(metadata_path):
		SetMetadata({
		    "workshop_spotlight_cycle": {
		        "current_index": 0,
		        "last_updated": 0
		    },
		    "last_webpanel_restart": 0,
		    "last_update_completed": 0,
		    "demonym_cycle": {
		        "current_index": 0,
		        "last_updated": 0
		    },
		    "error_status": {
		        "sidebar_length": {
		            "active": False,
		            "chars_over": 0
		        },
		        "stylesheet_length": {
		            "active": False,
		            "chars_over": 0
		        }
		    },
		    "community_spotlight_cycle": {
		        "current_index": 0,
		        "last_updated": 0
		    },
		    "header_cycle": {
		        "current_index": 0,
		        "last_updated": 0
		    }
		});
	return json.loads(open(metadata_path, 'r').read())

def SetMetadata(data):
	with io.open(metadata_path, 'w', encoding='utf-8') as f:
		f.write(unicode(json.dumps(data, ensure_ascii=False, indent=4, separators=(',', ': '))))